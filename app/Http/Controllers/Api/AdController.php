<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\AdsSeenHistory;
use App\Models\User;
use App\Models\FavouriteAds;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'address' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
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
                $ads = Advertisement::with('subCategory', 'category','getRetingReview','media')->where(['status' => true, 'approved' => true, 'published' => true])->where('user_id','!=',$user->id);
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
                $ads->map(function($q){
                    $q->seen_count = (count($q->getSeenHistory) > 0) ? $q->getSeenHistory->count() : 0;
                    unset($q->getSeenHistory);
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

    public function myAds() {
        $ads = Advertisement::with('subCategory', 'category')->where(['user_id' => auth()->id(),'status' => true, 'approved' => true, 'published' => true])->latest()->get();
        $ads = collect($ads)->map(function($q) {
            $q->seen_count = (count($q->getSeenHistory) > 0) ? $q->getSeenHistory->count() : 0;
            $q->media = ($q->getFirstMediaUrl('ads'));
            unset($q->getSeenHistory);
            return $q;
        });
        return $this->SuccessResponse(200, 'Advertisement Fetched Successfully', $ads);
    }

    // Get app address for particular user   
    public function published(Request $request)
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

                $advertisement = Advertisement::where('id',$request->advertisent_id)->where('user_id',$user->id)->first();
                if(!empty($advertisement)){
                    if($advertisement->approved == 1){
                        $advertisement->published = 1;
                        $advertisement->save();
                        return $this->SuccessResponse(200, 'Advertisement published successfully');
                    }
                    return $this->ErrorResponse(401, 'Your advertisement can not approved by admin. Please contact your administrator.');
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

                $advertisement = Advertisement::with('subCategory', 'category','user')->where('id',$request->advertisent_id)->first();
                if(!empty($advertisement)){
                    $favotiteAds = FavouriteAds::where(['user_id' => $user->id,'ads_id' => $advertisement->id])->first();
                    $advertisement->seen_count = (count($advertisement->getSeenHistory) > 0) ? $advertisement->getSeenHistory->count() : 0;
                    $advertisement->favorite = $favotiteAds;
                    $advertisement->media = $advertisement->getFirstMediaUrl('ads');

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
                $user = User::with(['getPublishedAdv' => function($query){
                    $query->where(['status' => true, 'approved' => true, 'published' => true]);
                },'getPublishedAdv.media'])->where('id',$request->user_id)->first();
                if(!empty($user)){
                    return $this->SuccessResponse(200, 'Owner details get successfully',$user);
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
                    'address' => 'required|string',
                    'latitude' => 'required|string',
                    'longitude' => 'required|string',
                    'image.*' => 'mimes:jpeg,png,jpg'
                ]);
                if ($validator->fails()) {
                    return $this->ErrorResponse(403, 'Input Validation Failed', $validator->errors());
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
                    return $this->SuccessResponse(200, 'Ad Sent for approval', $advertisement);
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

    // Add Advertisemnd
    public function addAdsHistory($user,$advertisent_id) {
        $adsSeenHistory = new AdsSeenHistory;
        $adsSeenHistory->user_id = $user->id;
        $adsSeenHistory->ads_id = $advertisent_id;
        $adsSeenHistory->save();
    }

       //aAd Rating Review
    //    public function addRating(Request $request) {
    //     try {
    //         $user = auth()->user();
    //         if(!empty($user)){
    //             $validator = Validator::make($request->all(), [
    //                 'advertisent_id'=>'required|integer|exists:advertisements,id',
    //                 'rating'=>'required|integer',
    //             ]);
    //             if ($validator->fails()) {
    //                 return $this->ErrorResponse(400,$validator->errors()->first());
    //             }
    //             $advertisent = Advertisement::where('id',$request->advertisent_id)->first();
    //             if(!empty($advertisent)){
    //                 $getRating = AdsRating::where(['user_id' => $user->id,'ads_id' => $advertisent->id])->first();
    //                 if(!empty($getRating)){
    //                     return $this->ErrorResponse(403, 'You have already give rating and review');
    //                 }
    //                 $adsRating = new AdsRating;
    //                 $adsRating->user_id = $user->id;
    //                 $adsRating->owner_id = $advertisent->user_id;
    //                 $adsRating->ads_id = $advertisent->id;
    //                 $adsRating->rating = $request->rating;
    //                 $adsRating->review = $request->review;
    //                 $adsRating->save();
    //                 return $this->SuccessResponse(200, 'Rating review added successfully!', $adsRating);
    //             }   
    //             return $this->ErrorResponse(404, 'Advertisement not found');
    //         }
    //         return $this->ErrorResponse(500, 'Something Went Wrong');
    //     } catch (Exception $exception) {
    //         logger('error occurred in addresses fetching process');
    //         logger(json_encode($exception));
    //         return $this->ErrorResponse(500, 'Something Went Wrong');
    //     }
    // }
}
