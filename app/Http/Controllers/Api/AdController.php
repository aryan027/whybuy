<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\AdsSeenHistory;
use App\Models\User;
use App\Models\FavouriteAds;
use App\Models\RecentSearchAds;
use App\Models\RentItem;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdController extends Controller
{
    protected $category, $subCategory;

    public function __construct() {
        $this->category = Category::where(['status' => true])->get()->map(function ($sub){
            $sub->images= $sub->image;
            unset($sub['media']);
            return $sub;
        });
        $this->subCategory = SubCategory::where(['status' => true])->get()->map(function ($sub){
            $sub->images= $sub->image;
            unset($sub['media']);
            return $sub;
        });
    }

    public function createAdvertisement(Request $request) {
        $validator = Validator::make($request->all(), [
            'category' => 'required|exists:categories,id',
            'sub_category' => 'required|exists:sub_categories,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'brand' => 'required|string',
            'deposit_amount' => 'required|integer',
            'hourly_rent' => 'required|integer',
            'daily_rent' => 'required|integer',
            'weekly_rent' => 'required|integer',
            'monthly_rent' => 'required|integer',
            'yearly_rent' => 'required|integer',
            'item_condition' => 'required|string',
            'owner_type' => 'required|string',
            'address_id' => 'required|integer|exists:addresses,id',
            'purpose' => 'required',
            // 'image' => 'required|mimes:jpg,jpeg,png'
            'image' => 'required',
            'image.*' => 'image|mimes:jpeg,png,jpg'
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(400,$validator->errors()->first());
            // return $this->ErrorResponse(403, 'Input Validation Failed', $validator->errors());
        }
        $request['user_id'] = auth()->id();
        $request['status'] = 1;
        $request['published'] = 1;
        $request['ad_id'] = IdGenerator::generate(['table' => 'advertisements','field'=>'ad_id', 'length' => 16, 'prefix' => date('Y').'-'.auth()->id().'-']);
        $create = Advertisement::create($request->all());
        if (!$create) {
            return $this->ErrorResponse(500, 'Unable to Insert records');
        }
        if ($request->hasFile('image')) {
            foreach ($request->image as $image)
            {
                $create->addMedia($image)->toMediaCollection('ads');
            }
        }
        return $this->SuccessResponse(200, 'Ad Sent for approval', $create);
    }

    // Ad List
    public function adsListing(Request $request) {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $ads = Advertisement::with('subCategory', 'category','media','getReview')->where(['status' => true, 'approved' => true, 'published' => true])->where('user_id','!=',$user->id);
                $search = $request->search;
                $category = $request->category;
                $subCategory = $request->subCategory;
                $start_budget = $request->start_budget;
                $end_budget = $request->end_budget;
                $latitude = $request->latitude;
                $longitude = $request->longitude;
                if($search){
                    $ads = $ads->where('title','LIKE','%'.$search.'%');
                }
                if($category){
                    $ads = $ads->whereHas('category',function($q) use($category){
                        $q->where('name','LIKE','%'.$category.'%');
                    });
                }
                if($subCategory){
                    $ads = $ads->whereHas('subCategory',function($q) use($subCategory){
                        $q->where('name','LIKE','%'.$subCategory.'%');
                    });
                }
                if($start_budget && $end_budget){
                    $ads = $ads->whereBetween('deposit_amount',[$start_budget,$end_budget]);
                }

                if($latitude && $longitude){
                    $ads = $ads->select('*', \DB::raw(sprintf(
                        '(6371 * acos(cos(radians(%1$.7f)) * cos(radians(`latitude`)) * cos(radians(`longitude`) - radians(%2$.7f)) + sin(radians(%1$.7f)) * sin(radians(`latitude`)))) AS distance',
                        $latitude,
                        $longitude
                    )))
                    ->having('distance', '<', 10)
                    ->orderBy('distance', 'asc');
                }

                $ads = $ads->latest()->paginate(20);
                $ads->map(function($q) use($search,$category,$subCategory,$start_budget,$end_budget,$latitude,$longitude,$user){
                    $q->seen_count = (count($q->getSeenHistory) > 0) ? $q->getSeenHistory->count() : 0;
                    unset($q->getSeenHistory);
                    if($search !='' || $category !='' || $subCategory !='' || ($start_budget !=''  && $end_budget !='') || ($latitude !='' && $longitude !='')){
                        $this->resentSearchStore($q->id,$user->id);
                    }
                });
                // $ads = collect($ads)->map(function($q) {
                //     $q->media = ($q->getFirstMediaUrl('ads'));
                //     return $q;
                // });
                return $this->SuccessResponse(200, 'Advertisement Fetched Successfully', $ads);
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    public function resentSearchStore($adsId,$userId)
    {
        $getRecentAds =  RecentSearchAds::where(['user_id' => $userId, 'ad_id' => $adsId])->first();
        $recentSearchAds = new RecentSearchAds;
        if(!empty($getRecentAds)){
            $recentSearchAds = $getRecentAds;
        }
        $recentSearchAds->user_id = $userId;
        $recentSearchAds->ad_id = $adsId;
        $recentSearchAds->updated_at = Carbon::now();
        $recentSearchAds->save();
    }

    public function myAds() {
        $ads = Advertisement::with('subCategory', 'category','address','getReview')->where(['user_id' => auth()->id(),'status' => true, 'published' => true])->latest()->get();
        $ads = collect($ads)->map(function($q) {
            $q->seen_count = (count($q->getSeenHistory) > 0) ? $q->getSeenHistory->count() : 0;
            $q->media = ($q->getFirstMediaUrl('ads'));
            unset($q->getSeenHistory);
            return $q;
        });
        return $this->SuccessResponse(200, 'Advertisement Fetched Successfully', $ads);
    }

    // Get Advertisement Detail
    public function AdvertisementDetail(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'advertisent_id'=>'required|integer|exists:advertisements,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }

                $advertisement = Advertisement::with('subCategory', 'category','user','user.media','address')->where('id',$request->advertisent_id)->first();
                if(!empty($advertisement)){
                    $favotiteAds = FavouriteAds::where(['user_id' => $user->id,'ads_id' => $advertisement->id])->first();
                    $advertisement->seen_count = (count($advertisement->getSeenHistory) > 0) ? $advertisement->getSeenHistory->count() : 0;
                    $advertisement->favorite = $favotiteAds;
                    $advertisement->review_rating = $advertisement->getReview;
                    $advertisement->media = $advertisement->getFirstMediaUrl('ads');
                    $getSimilarProduct  = Advertisement::with('media')->where('sub_category',$advertisement->sub_category)->where('id','!=',$advertisement->id)->orderBy('id','DESC')->limit(10)->get();
                    $advertisement->similar_product = $getSimilarProduct;
                    $adsSeenHistory = AdsSeenHistory::where(['user_id' => $user->id,'ads_id' => $advertisement->id])->first();
                    if(empty($adsSeenHistory)){
                        $this->addAdsHistory($user,$advertisement->id);
                    }

                    unset($advertisement->getSeenHistory);
                    return $this->SuccessResponse(200, 'Advertisement detail get successfully',$advertisement);
                }
                return $this->ErrorResponse(404, 'Advertisement not found');
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Get Owner Profile
    public function ownerProfile(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'user_id'=>'required|integer|exists:users,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $userOwner = User::with(['getPublishedAdv' => function($query){
                    $query->where(['status' => true, 'approved' => true, 'published' => true]);
                },'getPublishedAdv.media'])->where('id',$request->user_id)->first();
                if(!empty($userOwner)){
                    $userOwner->profile_picture = $userOwner->getFirstMediaUrl('profile_picture');
                    unset($userOwner->media);
                    return $this->SuccessResponse(200, 'Owner details get successfully',$userOwner);
                }
                return $this->ErrorResponse(404, 'Advertisement not found');
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Update Advertisement
    public function updateAdvertisement(Request $request) {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator = Validator::make($request->all(), [
                    'advertisent_id'=>'required|integer|exists:advertisements,id',
                    'category' => 'required|exists:categories,id',
                    'sub_category' => 'required|exists:sub_categories,id',
                    'title' => 'required|string',
                    'description' => 'required|string',
                    'brand' => 'required|string',
                    'deposit_amount' => 'required|integer',
                    'hourly_rent' => 'required|integer',
                    'daily_rent' => 'required|integer',
                    'weekly_rent' => 'required|integer',
                    'monthly_rent' => 'required|integer',
                    'yearly_rent' => 'required|integer',
                    'item_condition' => 'required|string',
                    'owner_type' => 'required|string',
                    'address_id' => 'required|integer|exists:addresses,id',
                    // 'image.*' => 'mimes:jpeg,png,jpg'
                ]);
                if ($validator->fails()) {
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $advertisement = Advertisement::where('id',$request->advertisent_id)->where('user_id',$user->id)->first();
                if(!empty($advertisement)){
                    $advertisements = $advertisement->update($request->all());
                    if (!$advertisements) {
                        return $this->ErrorResponse(500, 'Unable to Insert records', $create);
                    }
                    if ($request->hasFile('image')) {
                        foreach ($request->image as $image)
                        {
                            $advertisement->addMedia($image)->toMediaCollection('ads');
                        }
                    }
                    return $this->SuccessResponse(200, 'Ads updated successfully', $advertisement);
                }
                return $this->ErrorResponse(404, 'Advertisement not found');
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }
    public function AdvertisementBySub(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'sub_category'=>'required|integer',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }
                $ads = Advertisement::with('subCategory', 'category')->where(['sub_category' => $request['sub_category'],'status' => true, 'approved' => true, 'published' => true])->get()->paginate(20);
                //  $ads = collect($ads)->map(function($q) {
                //                    $q->media = ($q->getFirstMediaUrl('ads'));
                //                    return $q;
                //                });
                return $this->SuccessResponse(200, 'Advertisement Fetched Successfully', $ads);
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in ads fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    public function addAdsHistory($user,$advertisementId)
    {
       $adsSeenHistory = new AdsSeenHistory;
       $adsSeenHistory->user_id = $user->id;
       $adsSeenHistory->ads_id = $advertisementId;
       $adsSeenHistory->save();
    }

    // Recent Search
    public function recentSearch(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $getRecentAds =  RecentSearchAds::with('recentSearchAds','recentSearchAds.media')->where(['user_id' => $user->id])->orderBy('updated_at','DESC')->paginate(20);
                return $this->SuccessResponse(200, 'Advertisement Fetched Successfully', $getRecentAds);
            }
            return $this->ErrorResponse(401, 'Unauthorized');
        } catch (Exception $exception) {
            logger('error occurred in ads fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Trending
    public function trending(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $trending =  RentItem::select(\DB::raw('count(id) as total,ads_id'))
                ->with(
                ['ads' => function($q){
                    $q->select('id','sub_category');
                },'ads.getSubCategory.media'])
                ->orderBy('total','DESC')->groupBy('ads_id')->paginate(20);
                return $this->SuccessResponse(200, 'Tranding Fetched Successfully', $trending);
            }
            return $this->ErrorResponse(401, 'Unauthorized');
        } catch (Exception $exception) {
            logger('error occurred in ads fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Trending
    public function deleteAds(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'ads_id'=>'required|integer|exists:advertisements,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }
                $ads = Advertisement::where('id',$request->ads_id)->where('user_id',$user->id)->first();
                if(!empty($ads)){
                    $adsOrder = $ads->getRentOrder->count();
                    if($adsOrder > 0){
                        return $this->ErrorResponse(200, 'You can not delete this Advertisement.Becuase this Advertisement provide by multiple orders.');
                    }
                    $ads->delete();
                    return $this->SuccessResponse(200, 'Advertisement deleted Successfully');
                }
                return $this->ErrorResponse(200, 'Advertisement Not Found');
            }
            return $this->ErrorResponse(401, 'Unauthorized');
        } catch (Exception $exception) {
            logger('error occurred in ads fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    
    
}
