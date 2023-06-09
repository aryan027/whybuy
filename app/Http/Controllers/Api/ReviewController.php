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
            'rent_id'=>'required|integer',
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
            'rent_id'=>$request['rent_id'],
            'rating'=>$request['rating'],
            'review'=>$request['review'],
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
        $list= Review::with('ads','rent')->where(['ad_id'=>$request->ad_id])->get();
        return $this->SuccessResponse(200,'Data fetch successfully ..!',$list);
    }
    public function review_user_list(Request $request){

        $list= Review::with('ads','rent')->where(['user_id'=>auth()->id()])->get();
        return $this->SuccessResponse(200,'Data fetch successfully ..!',$list);
    }

    public function review_edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'=>'required',
            'rating' => 'required_without:review|integer|between:1,5',
            'review' => 'required_without:rating|string|max:255',
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(200,$validator->errors()->first());
        }
        if(!Review::where('id',$request['id'])->exists()){
            return $this->ErrorResponse(200,'Invalid review Id ..!');
        }

        $review= Review::where('id',$request['id'])->get()->first();
        $review->rating= $request->rating;
        $review->review= $request->review;
        $review->save();
        return $this->SuccessResponse(200,'Review updated successfully ..!',$review);

    }
}
