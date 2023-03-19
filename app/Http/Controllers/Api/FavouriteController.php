<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FavouriteAds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class FavouriteController extends Controller
{
    public function addToFavourite(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'ads_id'=> 'required|integer|exists:advertisements,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }
                $getFevorite = FavouriteAds::where('ads_id',$request->ads_id)->where('user_id',$user->id)->first();
                if(!empty($getFevorite)){
                    return $this->ErrorResponse(200, 'You have already favorite this advertisement');
                }
                $favourite = FavouriteAds::create([
                    'ads_id'=>$request->ads_id,
                    'user_id'=>$user->id
                ]);
                return $this->SuccessResponse(200,'Added to favourite successfully',$favourite);
            }
            return $this->ErrorResponse(200, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this ->ErrorResponse(200, 'Something Went Wrong');
        }
    }

    public function myFavouriteList(){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $favouriteAds= FavouriteAds::with('user','ads')->where('user_id',$user->id)->latest()->get();
                return $this->SuccessResponse(200,'Data fetch successfully ..',$favouriteAds);
            }
            return $this->ErrorResponse(200, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this ->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    public function myFavouriteRemove(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'favotite_id'=> 'required|integer|exists:favourite_ads,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }
                $favouriteAds= FavouriteAds::where(['id' => $request->favotite_id,'user_id' => $user->id])->first();
                if(!empty($favouriteAds)){
                    $favouriteAds->delete();
                    return $this->SuccessResponse(200,'Remove successfully.',);
                }
                return $this->ErrorResponse(200, 'Favorite not found');
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this ->ErrorResponse(500, 'Something Went Wrong');
        }
    }

}
