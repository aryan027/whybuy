<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ad_id'=>'required',
            'rating' => 'required_without:review|integer|between:1,5',
            'review' => 'required_without:rating|string|max:255',
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(200,$validator->errors()->first());
        }
        if (Review::where(['user_id'=>auth()->id(),'ad_id'=> $request->ad_id])->exists()) {
            return $this->ErrorResponse(200,'You have already submitted a review ');
        }
        // Create the new app review
        $review = Review::create([
            'user_id'=>auth()->id(),
            'rating'=>$request['rating'],
            'comment'=>$request['comment'],
            'ad_id'=>$request['ad_id']
        ]);
        return $this->SuccessResponse(200,'Review given successfully ..!',$review);

    }

    public function review_list(Request $request){
        $validator = Validator::make($request->all(), [
            'ad_id'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(200,$validator->errors()->first());
        }
        $list= Review::with('ads')->where(['ad_id'=>$request->ad_id])->get();
        return $this->SuccessResponse(200,'Data fetch successfully ..!',$list);
    }
    public function review_user_list(Request $request){
        $validator = Validator::make($request->all(), [
            'ad_id'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(200,$validator->errors()->first());
        }
        $list= Review::with('ads')->where(['ad_id'=>$request->ad_id,'user_id'=>auth()->id()])->get();
        return $this->SuccessResponse(200,'Data fetch successfully ..!',$list);
    }
}
