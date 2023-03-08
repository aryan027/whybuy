<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FavouriteAds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavouriteController extends Controller
{
    public function addToFavourite(Request $request){
        $validator= Validator::make($request->all(),[
            'ads_id'=>'required'
        ]);
        if($validator->fails()){
            return $this->ErrorResponse(400,$validator->errors()->first());
        }
        $favourite = FavouriteAds::create([
           'ads_id'=>$request->ads_id,
           'user_id'=>auth()->id()
        ]);
        return $this->SuccessResponse(200,'Added to favourite successfully',$favourite);
    }

    public function myFavouriteList(){
        $ads= FavouriteAds::with('user','ads')->latest()->get();
        return $this->SuccessResponse(200,'Data fetch successfully ..',$ads);
    }
}
