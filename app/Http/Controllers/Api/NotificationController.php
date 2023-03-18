<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'status'=>'required|in:1,2,3,4',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $notification = $this->notification::with('getRentItem','getRentItem.ads','getRentItem.ads.media','getSenderUser','getSenderUser.media')->where(['receiver_id' => $user->id,'status' => $request->status])->paginate(20);
                return  $this->SuccessResponse(200,'Notification Fetched!',$notification);  
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this ->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    public function readNotification(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'notification_id'=>'required|integer|exists:notification,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }

                $notification = $this->notification::where(['receiver_id' => $user->id,'id' => $request->notification_id])->first();
                if(!empty($notification)){
                    $notification->is_read = $this->notification::IS_READ;
                    $notification->save();
                    return  $this->SuccessResponse(200,'Notification read successfully!',$notification);  
                }
                return $this->ErrorResponse(404, 'Notification not found');
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this ->ErrorResponse(500, 'Something Went Wrong');
        }
    }

}
