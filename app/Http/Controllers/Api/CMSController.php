<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class CMSController extends Controller
{
    public function cms(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'type'=>'required|in:terms_condition,privacy_policy,abount_us',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $cms = $this->cms->where('type',$request->type)->first();
                return $this->SuccessResponse(200, 'cms get Successfully',$cms);
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in addresses fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }
}
