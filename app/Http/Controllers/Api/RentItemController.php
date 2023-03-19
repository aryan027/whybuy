<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\RentItem;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;
use DateTime;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DatePeriod;
use Storage;
use PDF;

class RentItemController extends Controller
{
//    protected $rentItem;
//    public function __construct() {
//        $this->rentItem = Advertisement::where(['status' => 0])->latest()->get();
//    }
    public function rentItem(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'ads_id'=>'required|integer|exists:advertisements,id',
                    'rent_type'=>'required|in:day',
                    'start'=>'required|date_format:Y-m-d H:i|after_or_equal:today',
                    'end' =>'required|date_format:Y-m-d H:i|after_or_equal:start',
                    'description'=>'nullable',
                    'purpose'=>'nullable'
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }

                $advertisement = Advertisement::where('id', $request->ads_id)->first();
                if(!empty($advertisement)){
                    $wallet = Wallet::where('user_id',$user->id)->first();
                    if(!empty($wallet) && $wallet->balance >= $advertisement->deposit_amount){
                        // $startDate = Carbon::createFromFormat('Y-m-d', $request->start);
                        // $endDate = Carbon::createFromFormat('Y-m-d', $request->end);
                        // $dateRange = CarbonPeriod::create($startDate, $endDate);
                        // $dates = array_map(fn ($date) => $date->format('Y-m-d'), iterator_to_array($dateRange));

                        // $time = [];
                        // if($request->rent_type == 'day'){
                        //     foreach($dates as $date){
                        //         $rentItem = RentItem::whereDate('start', $date)->where(['ads_id' => $request->ads_id,'owner_id' => $advertisement->user_id])->first();
                        //         if(!empty($rentItem)){
                        //             return $this->ErrorResponse(400, 'This item already booked on this date '.$date);
                        //         }
                        //     }
                        // }
                        $price = $advertisement->daily_rent;
                        $item= RentItem::create([
                            'ads_id'=>$request->ads_id,
                            'user_id'=> $user->id,
                            'owner_id' => $advertisement->user_id,
                            'rent_type'=>$request->rent_type,
                            'start'=> $request->start,
                            'end' => $request->end,
                            'price'=> $price,
                            'deposite_amount' => $advertisement->deposit_amount,
                            'description'=>$request->description,
                            'purpose'=>$request->purpose,
                            'block' => 0
                        ]);
                        if($item){
                            $type = 'send_for_accept_agreement';
                            $message = 'Accept agreement request for you';
                            $status = 1; // request
                            $senderId = $item->user_id;
                            $receiverId = $item->owner_id;
                            $this->storeNotification($senderId,$receiverId, $status ,$item->id,$type,$message);
                        }
                        return $this->SuccessResponse(200,'Rent Request send successfully',$item);
                    }
                    return $this->ErrorResponse(200, 'Please first add wallet amound base on deposit amount');
                }
                return $this->ErrorResponse(200, 'Advertisement not found');
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    //My Rent Item list
    public function myRentItemList(){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $rentitem= RentItem::with('ads','owners')->where('user_id',$user->id)->latest()->get();
                return $this->SuccessResponse(200, 'Rent list Fetched Successfully', $rentitem);
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Provide Rent ItemList
    public function provideRentItemList(){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $rentitem= RentItem::with('ads','users')->where('owner_id',$user->id)->latest()->get();
                return $this->SuccessResponse(200, 'Rent list Fetched Successfully', $rentitem);
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Rent Detail
    public function rentDetail(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'rent_id'=>'required|integer|exists:rent_items,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }
                $rentitem= RentItem::with('ads','users','owners')->where('id',$request->rent_id)->first();
                if(!empty($rentitem)){
                    return $this->SuccessResponse(200, 'Rent detail Fetched Successfully', $rentitem);
                }
                return $this->ErrorResponse(200, 'Rent Not Found');
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Time Slot Modules
    public function timeSlot(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'ads_id'=>'required|integer|exists:advertisements,id',
                    'rent_type'=>'required|in:day',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }
                $advertisement = Advertisement::where('id',$request->ads_id)->first();
                if(!empty($advertisement)){
                    // $startDate = Carbon::createFromFormat('Y-m-d', $request->start);
                    // $endDate = Carbon::createFromFormat('Y-m-d', $request->end);
                    // $dateRange = CarbonPeriod::create($startDate, $endDate);
                    // $dates = array_map(fn ($date) => $date->format('Y-m-d'), iterator_to_array($dateRange));
                    $time = [];
                    if($request->rent_type == 'day'){
                        $startDate = Carbon::now()->format('Y-m-d');
                            $endDate = Carbon::now()->addMonths(3)->format('Y-m-d');
                            $rentItem = RentItem::whereBetween(\DB::raw("(DATE_FORMAT(start,'%Y-%m-%d'))"), [$startDate, $endDate])->where(['ads_id' => $request->ads_id,'owner_id' => $advertisement->user_id])->get();
                            $storeDate = [];
                            foreach($rentItem as $key => $value){
                                $storeDate[$key][] = Carbon::parse($value->start)->format('Y-m-d').' | '.Carbon::parse($value->end)->format('Y-m-d');
                            }
                            $datesData = [];
                            foreach($storeDate as $date){
                                $storeDate = explode('|',$date[0]);
                                $dateRange = CarbonPeriod::create($storeDate[0], $storeDate[1]);
                                $timeData = array_map(fn ($date) => $date->format('Y-m-d'), iterator_to_array($dateRange));
                                $time = array_merge($time,$timeData);
                            }
                            return $this->SuccessResponse(200, 'Time slot get successfully',$time);
                        }
                    return $this->ErrorResponse(200, 'The rent type field is required.');
                }
                return $this->ErrorResponse(200, 'Advertisement not found');
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Agreement Form
    public function agreementForm(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'rent_id'=>'required|integer|exists:rent_items,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }
                $rentitem= RentItem::where('id',$request->rent_id)->first();

                if(!empty($rentitem)){
                    $data = [
                        'rent_item_id' => $rentitem->id,
                        'url' => url('/').'/agreement-form/'.$rentitem->id.'/'.$user->id,
                    ];

                    return $this->SuccessResponse(200, 'Rent detail Fetched Successfully', $data);
                }
                // $str='<html><body>';
                // $str.='<p>Name</p><p>Phone</p><p>Address</p><p>Date Start</p><p>Date End</p><p>Time</p><p>Purpose</p>';
                // $str.='</body></html>';
                // return  $this->SuccessResponse(200,'Data fetch successfully ..',$str);
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }

    }

    // Owner Accept Agreement
    public function ownerAcceptAgreement(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'rent_item_id'=>'required|integer|exists:rent_items,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }
                $rentItem = RentItem::where(['id' => $request->rent_item_id,'owner_id' => $user->id])->first();
                if(!empty($rentItem)){
                    if($rentItem->status == 3){
                        return $this->ErrorResponse(200, 'your rent agreement is already canceled');
                    }
                    // $getRentalAgreement = $this->rentalAgreement->where(['rent_item_id' => $rentItem->id])->first();
                    // if(!empty($getRentalAgreement)){
                    //     return $this->ErrorResponse(409, 'You are already confirmed rent agreement');
                    // }
                    $rentalAgreement = $this->rentalAgreement;
                    $rentalAgreement->rent_item_id = $rentItem->id;
                    $rentalAgreement->is_accept = 1;
                    $rentalAgreement->owner_id = $user->id;
                    // $jsonobj = '{"east_shelter_house":true,"west_shelter_house":false,"jayeee":false,"east_shelter_house_kitchen":false,"crown_pavilion":false,"water":false}';
                    $rentalAgreement->save();
                    if($rentalAgreement){
                        $rentItem->status = 1; //Accepted by owner side
                        $rentItem->save();
                        if($rentItem){
                            $type = 'accepted_agreement';
                            $message = 'Accept agreement from owner side. please accept agreement';
                            $status = 2; //Approved
                            $senderId = $rentItem->owner_id;
                            $receiverId = $rentItem->user_id;
                            $this->storeNotification($senderId,$receiverId,$status,$rentItem->id,$type,$message);
                        }
                    }
                    return  $this->SuccessResponse(200,'Rental agreement accepted.');
                }
               return $this->ErrorResponse(200, 'Invalid rent form');
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this ->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // User Confirm Agreement
    public function userConfirmAgreement(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'rent_item_id'=>'required|integer|exists:rent_items,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }
                $rentItem = RentItem::where(['id' => $request->rent_item_id,'user_id' => $user->id])->first();
                if(!empty($rentItem)){
                    if($rentItem->status == 3){
                        return $this->ErrorResponse(200, 'your rent agreement is already canceled');
                    }
                    $getRentalAgreement = $this->rentalAgreement->where(['rent_item_id' => $rentItem->id])->first();
                    if(!empty($getRentalAgreement)){
                        $getRentalAgreement->is_confirm = 1;
                        $getRentalAgreement->user_id = $user->id;
                        $getRentalAgreement->save();
                        if($getRentalAgreement){

                            $fileName = Carbon::now()->format('YmdHis').'_'.$user->id.'.pdf';
                            $pdf = PDF::loadView('invoice.invoice-form',compact('rentItem'));
                            $content = $pdf->download()->getOriginalContent();
                            // Storage::put('public/invoice/'.$fileName,$content);
                            $disk = 'public_local';
                            $storagePath = Storage::disk($disk)->put('invoice/'.$fileName,$content);
                            $rentItem->invoice = 'invoice/'.$fileName;

                            $rentItem->status = 2; //Accept by user side
                            $rentItem->save();
                            if($rentItem){
                                $type = 'confirm_agreement';
                                $message = 'Agreement confirmed';
                                $status = 2; //Approved
                                $senderId = $rentItem->user_id;
                                $receiverId = $rentItem->owner_id;
                                $this->storeNotification($senderId,$receiverId,$rentItem->id,$type,$message);
                            }
                            $data = [
                                'invoice' => asset('/').$rentItem->invoice
                            ];

                            return  $this->SuccessResponse(200,'Rental agreement confirmed.',$data);
                        }
                        return $this->ErrorResponse(200, 'Something went to wrong!');
                    }
                    return $this->ErrorResponse(200, 'Owner can not acccepted rent agreement');
                }
                return $this->ErrorResponse(200, 'Invalid rent form');
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // Invoice
    public function invoice(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'rent_item_id'=>'required|integer|exists:rent_items,id',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(200,$validator->errors()->first());
                }
                $rentItem = RentItem::where(['id' => $request->rent_item_id,'user_id' => $user->id])->first();
                if(!empty($rentItem)){
                    $data = [
                        'invoice' => asset('/').$rentItem->invoice
                    ];

                    return  $this->SuccessResponse(200,'Invoice get successfully.',$data);
                }
                return $this->ErrorResponse(200, 'Invalid rent form');
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }


}
