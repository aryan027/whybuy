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
                    'start'=>'required|date_format:Y-m-d|after_or_equal:today',
                    'end' =>'required|date_format:Y-m-d|after_or_equal:start',
                    // 'price'=>'required',
                    'description'=>'nullable',
                    'purpose'=>'nullable'
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }

                // if($request->rent_type == 'hour'){
                //     $validator= Validator::make($request->all(),[
                //         'start_time'=>'required|date_format:H:i',
                //         'end_time' =>'required|date_format:H:i|after:start_time',
                //     ]);
                //     if($validator->fails()){
                //         return $this->ErrorResponse(400,$validator->errors()->first());
                //     }
                // }

                $advertisement = Advertisement::where('id', $request->ads_id)->first();
                if(!empty($advertisement)){
                    $wallet = Wallet::where('user_id',$user->id)->first();
                    if(!empty($wallet) && $wallet->balance >= $advertisement->deposit_amount){
                        $startDate = Carbon::createFromFormat('Y-m-d', $request->start);
                        $endDate = Carbon::createFromFormat('Y-m-d', $request->end);
                        $dateRange = CarbonPeriod::create($startDate, $endDate);
                        $dates = array_map(fn ($date) => $date->format('Y-m-d'), iterator_to_array($dateRange));
                    
                        $time = [];
                        if($request->rent_type == 'day'){
                            foreach($dates as $date){
                                $rentItem = RentItem::whereDate('start', $date)->where(['ads_id' => $request->ads_id,'owner_id' => $advertisement->user_id])->first();
                                if(!empty($rentItem)){
                                    return $this->ErrorResponse(400, 'This item already booked on this date '.$date);
                                }
                            }
                        }
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
                            'purpose'=>$request->purpose
                        ]);
                        return $this->SuccessResponse(200,'Rent Request send successfully',$item);
                    }
                    return $this->ErrorResponse(400, 'Please first add wallet amound base on deposit amount');
                }
                return $this->ErrorResponse(404, 'Advertisement not found');
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
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $rentitem= RentItem::with('ads','users','owners')->where('id',$request->rent_id)->first();
                if(!empty($rentitem)){
                    return $this->SuccessResponse(200, 'Rent detail Fetched Successfully', $rentitem);
                }
                return $this->ErrorResponse(404, 'Rent Not Found');
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
                    'owner_id'=>'required|integer|exists:users,id',
                    'ads_id'=>'required|integer|exists:advertisements,id',
                    'rent_type'=>'required|in:day',
                    'start'=>'required|date_format:Y-m-d|after_or_equal:today',
                    'end' =>'required|date_format:Y-m-d|after_or_equal:start',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }

                $startDate = Carbon::createFromFormat('Y-m-d', $request->start);
                $endDate = Carbon::createFromFormat('Y-m-d', $request->end);

                $dateRange = CarbonPeriod::create($startDate, $endDate);

                $dates = array_map(fn ($date) => $date->format('Y-m-d'), iterator_to_array($dateRange));
               
                $time = [];
                if($request->rent_type == 'day'){
                    foreach($dates as $date){
                        $rentItem = RentItem::whereDate('start', $date)->where(['ads_id' => $request->ads_id,'owner_id' => $request->owner_id])->first();
                        if(!empty($rentItem)){
                            $time[$date]['status'] = 'Not availiable';
                        } else {
                            $time[$date]['status'] = 'availiable';
                        }
                    }
                    return $this->SuccessResponse(200, 'Time slot get successfully',$time);
                }
                return $this->ErrorResponse(400, 'The rent type field is required.');

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
                $str='<html><body>';
                $str.='<p>Name</p><p>Phone</p><p>Address</p><p>Date Start</p><p>Date End</p><p>Time</p><p>Purpose</p>';
                $str.='</body></html>';
                return  $this->SuccessResponse(200,'Data fetch successfully ..',$str);  
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
        
    }

    // Owner Confirm Agreement
    public function ownerConfirmAgreement(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'rent_item_id'=>'required|integer|exists:rent_items,id',
                    'name'=>'required|string',
                    'phone'=>'required|numeric|digits:10',
                    'address'=>'required',
                    'start_date'=>'required|date_format:Y-m-d|after_or_equal:today',
                    'end_date' =>'required|date_format:Y-m-d|after_or_equal:start',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $rentItem = RentItem::where(['id' => $request->rent_item_id,'owner_id' => $user->id])->first();
                if(!empty($rentItem)){
                    if($rentItem->status == 3){
                        return $this->ErrorResponse(400, 'your rent agreement is already canceled');
                    }
                    $getRentalAgreement = $this->rentalAgreement->where(['rent_item_id' => $rentItem->id,'user_id' => $user->id,'user_type' => $this->rentalAgreement::IS_OWNER_TYPE])->first();
                    if(!empty($getRentalAgreement)){
                        return $this->ErrorResponse(409, 'You are already confirmed rent agreement');
                    }
                    $rentalAgreement = $this->rentalAgreement;
                    $rentalAgreement->user_id = $user->id; //Owner id is users table
                    $rentalAgreement->user_type = 2;
                    $rentalAgreement->rent_item_id = $rentItem->id;
                    $rentalAgreement->name = $request->name;
                    $rentalAgreement->phone = $request->phone;
                    $rentalAgreement->address = $request->address;
                    $rentalAgreement->start_date = $request->start_date;
                    $rentalAgreement->end_date = $request->end_date;
                    $rentalAgreement->start_time = $request->start_time;
                    $rentalAgreement->end_time = $request->end_time;
                    $rentalAgreement->purpose = $request->purpose;
                    $rentalAgreement->shelter = $request->shelter; 
                    // $jsonobj = '{"east_shelter_house":true,"west_shelter_house":false,"jayeee":false,"east_shelter_house_kitchen":false,"crown_pavilion":false,"water":false}';
                    $rentalAgreement->save();
                    if($rentalAgreement){
                        $rentItem->status = 1; //Confirm by owner side
                        $rentItem->save();
                    }
                    return  $this->SuccessResponse(200,'Rental agreement confirmed.');  
                }
                return $this->ErrorResponse(401, 'Invalid rent form');
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    // User Accept Agreement
    public function userAcceptAgreement(Request $request){
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all(),[
                    'rent_item_id'=>'required|integer|exists:rent_items,id',
                    'name'=>'required|string',
                    'phone'=>'required|numeric|digits:10',
                    'address'=>'required',
                    'start_date'=>'required|date_format:Y-m-d|after_or_equal:today',
                    'end_date' =>'required|date_format:Y-m-d|after_or_equal:start',
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $rentItem = RentItem::where(['id' => $request->rent_item_id,'user_id' => $user->id])->first();
                if(!empty($rentItem)){
                    if($rentItem->status == 3){
                        return $this->ErrorResponse(400, 'your rent agreement is already canceled');
                    }
                    $getRentalAgreement = $this->rentalAgreement->where(['rent_item_id' => $rentItem->id,'user_id' => $user->id,'user_type' => $this->rentalAgreement::IS_USER_TYPE])->first();
                    if(!empty($getRentalAgreement)){
                        return $this->ErrorResponse(409, 'You are already acccepted rent agreement');
                    }
                    $rentalAgreement = $this->rentalAgreement;
                    $rentalAgreement->user_id = $user->id; //User id is users table
                    $rentalAgreement->user_type = 1;
                    $rentalAgreement->rent_item_id = $rentItem->id;
                    $rentalAgreement->name = $request->name;
                    $rentalAgreement->phone = $request->phone;
                    $rentalAgreement->address = $request->address;
                    $rentalAgreement->start_date = $request->start_date;
                    $rentalAgreement->end_date = $request->end_date;
                    $rentalAgreement->start_time = $request->start_time;
                    $rentalAgreement->end_time = $request->end_time;
                    $rentalAgreement->purpose = $request->purpose;
                    $rentalAgreement->shelter = $request->shelter; 
                    // $jsonobj = '{"east_shelter_house":true,"west_shelter_house":false,"jayeee":false,"east_shelter_house_kitchen":false,"crown_pavilion":false,"water":false}';
                    $rentalAgreement->save();
                    if($rentalAgreement){
                        $rentItem->status = 2; //Accept by user side
                        $rentItem->save();
                    }
                    return  $this->SuccessResponse(200,'Rental agreement accepted.');  
                }
                return $this->ErrorResponse(401, 'Invalid rent form');
            }
            return $this->ErrorResponse(401, 'Unauthenticated');
        } catch (Exception $exception) {
            logger('error occurred in user fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

}
