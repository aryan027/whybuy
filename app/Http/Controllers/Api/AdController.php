<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\SubCategory;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdController extends Controller
{
    protected $category, $subCategory;

    public function __construct() {
        $this->category = Category::where(['status' => true])->get();
        $this->subCategory = SubCategory::where(['status' => true])->get();
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
            'image' => 'required|mimes:jpg,jpeg,png'
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(403, 'Input Validation Failed', $validator->errors());
        }
        $request['user_id'] = auth()->id();
        $request['ad_id'] = IdGenerator::generate(['table' => 'advertisements','field'=>'ad_id', 'length' => 16, 'prefix' => date('Y').'-'.auth()->id().'-']);
        $create = Advertisement::create($request->all());
        if (!$create) {
            return $this->ErrorResponse(500, 'Unable to Insert records', $create);
        }
        if ($request->hasFile('image')) {
            foreach ($request->image as $image)
            {
                $create->addMedia($image)->toMediaCollection('ads');
            }

        }
        return $this->SuccessResponse(200, 'Ad Sent for approval', $create);
    }

    public function myAds() {
        $myAds = Advertisement::with('category', 'sub_category')->where(['user_id' => auth()->id()])->get()->map(function($listing){
            $images = [];
            foreach($listing->getMedia('ads') as  $media){
                $images[]= $media->getFullUrl('thumb');
            }
            $listing->images = $images;
            unset($listing['media']);
            return $listing;
        });
        $response = array(
            'all' => $myAds,
            'published' => $myAds->where('published', true),
            'in_queue' => $myAds->where('status', false)->where('published', false),
            'not_approved' => $myAds->where('approved', false),
            'approved' => $myAds->where('approved', true)
        );
        return $this->SuccessResponse(200, 'your ads fetched', $response);
    }
}
