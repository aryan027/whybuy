<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\Advertisement;
use App\Models\User;
use App\Models\Addresses;
use App\Models\Countries;
use App\Models\CMS;
use App\Models\RentalAgreement;
use App\Models\Notification;
use App\Models\AdsSeenHistory;
use App\Models\Package;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function __construct()
    {
        $this->user = new User();
        $this->category = new Category();
        $this->subCategory = new SubCategory();
        $this->childCategory = new ChildCategory();
        $this->advertisement = new Advertisement();
        $this->addresses = new Addresses();
        $this->countries = new Countries();
        $this->cms = new CMS();
        $this->wallet = new Wallet();
        $this->rentalAgreement = new RentalAgreement();
        $this->notification = new Notification();
        $this->adsSeenHistory = new AdsSeenHistory();
        $this->package = new Package();
    }
    public function SuccessResponse($code,$message,$data=null){

        $response=[];
        if( is_null($data)){
            $response= array(
                'success'=>true,
                'code'=>$code,
                'message'=>$message
            ) ;
        }else{
            $response=array(
                'success'=>true,
                'code'=>$code,
                'message'=>$message ?? ' No Record Found ..!' ,
                'data'=>$data
            );
        }

        return response($response,$code);
    }

    public function ErrorResponse($code,$message){
        $response=array(
            'success'=>false,
            'code'=>$code,
            'message'=>$message
        );
        return response()->json($response,$code);
    }

    public function response($data){
        if($data->count() ==0){
            return $this->SuccessResponse(200,"No record found ..!",$data);
        }
        return $this->SuccessResponse(200,"data fetch successfully ..!",$data);
    }

    public function storeNotification($senderId,$receiverId,$status,$rentItemId=Null,$type,$message)
    {
        $user = $this->user;
        if($senderId){
            $senderData = $user->where('id',$senderId)->first();
        }
        if($receiverId){
            $receiverData = $user->where('id',$receiverId)->first();
        }
        if(!empty($senderData) && !empty($receiverData)){
            $notification = $this->notification;
            $notification->sender_id = $senderData->id;
            $notification->receiver_id = $receiverData->id;
            $notification->rent_item_id = $rentItemId;
            $notification->type = $type;
            $notification->status = $status;
            $notification->message = $message;
            $notification->save();
            $this->SendMobileNotification($rentItemId,$receiverData,$message);
        }
    }

    public function storeAdvertisementNotification($senderId,$receiverId,$status,$ads_id=Null,$type,$message)
    {
        $user = $this->user;
        if($receiverId){
            $receiverData = $user->where('id',$receiverId)->first();
        }
        if(!empty($receiverData)){
            $notification = $this->notification;
            $notification->sender_id = $senderId;
            $notification->receiver_id = $receiverData->id;
            $notification->ads_id = $ads_id;
            $notification->type = $type;
            $notification->status = $status;
            $notification->message = $message;
            $notification->save();
            $this->SendMobileNotification($ads_id,$receiverData,$message);
        }
    }

    public function SendMobileNotification($rentItemId=NULL,$receiverData,$message=NULL)
    {
        try {
            $device_token = [$receiverData->device_token];
            if($device_token != ''){
                $SERVER_API_KEY = config('app.SERVER_API_KEY');
                $responseData = [
                    'rent_item_id' => $rentItemId,
                ];
                $data = [
                    "registration_ids" => $device_token,
                    "notification" => [
                        "priority" => "high",
                        "title" => config('app.name'),
                        "body" => $message,
                    ],
                    'data' => $responseData,
                    "content_available" => true,
                ];

                $dataString = json_encode($data);
                $headers = [
                    'Authorization: key='.$SERVER_API_KEY,
                    'Content-Type: application/json',
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                $response = curl_exec($ch);
                curl_close ( $ch );
            }

        } catch (Exception $e) {

        }

    }


    public function sendNotification($title, $body, $image = null, $token)
    {
        // FCM server key
        $serverKey =config('app.SERVER_API_KEY');
        $message = [
            'title' => $title,
            'body' => $body,
        ];
        if ($image) {
            $message['image'] = $image;
        }
        // FCM API endpoint
        $url = 'https://fcm.googleapis.com/fcm/send';

        // HTTP client
        $client = new Client();
        $headers = [
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        // HTTP body
        $body = [
            'notification' => $message,
            'to' => $token['device_token']
        ];

        // Send HTTP POST request
        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $body
        ]);

        // Get response body
        $responseBody = $response->getBody();

        // Return response
        return  json_decode($responseBody);
    }

//    public function sendNotification($title, $body, $image, $product, $token, $platform)
//    {
//        // FCM server key
//        $serverKey = 'your_server_key_here';
//
//        // Notification message
//        $message = [
//            'title' => $title,
//            'body' => $body,
//            'product' => $product,
//            'sound' => 'default'
//        ];
//
//        // FCM API endpoint
//        $url = 'https://fcm.googleapis.com/fcm/send';
//
//        // HTTP client
//        $client = new Client();
//
//        // HTTP headers
//        $headers = [
//            'Authorization' => 'key=' . $serverKey,
//            'Content-Type' => 'application/json',
//            'Accept' => 'application/json'
//        ];
//
//        // HTTP body
//        $body = [
//            'notification' => $message,
//            'to' => $token
//        ];
//
//        if($platform === 'android') {
//            $body['android'] = [
//                'priority' => 'high',
//                'notification' => $message
//            ];
//        } elseif($platform === 'ios') {
//            $body['apns'] = [
//                'payload' => [
//                    'aps' => [
//                        'alert' => [
//                            'title' => $title,
//                            'body' => $body
//                        ],
//                        'badge' => 1,
//                        'sound' => 'default'
//                    ]
//                ]
//            ];
//        }
//
//        // Send HTTP POST request
//        $response = $client->post($url, [
//            'headers' => $headers,
//            'json' => $body
//        ]);
//
//        // Get response body
//        $responseBody = $response->getBody();
//
//        // Return response
//        return response()->json([
//            'status' => 'success',
//            'response' => json_decode($responseBody)
//        ]);
//    }

}
