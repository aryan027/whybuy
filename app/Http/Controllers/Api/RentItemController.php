<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\RentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentItemController extends Controller
{
//    protected $rentItem;
//    public function __construct() {
//        $this->rentItem = Advertisement::where(['status' => 0])->latest()->get();
//    }
    public function rentItem(Request $request){
        $validator= Validator::make($request->all(),[
            'ads_id'=>'required',
            'rent_type'=>'required',
            'start'=>'required',
            'end' =>'required',
            'price'=>'required',
            'description'=>'nullable',
            'purpose'=>'nullable'
        ]);
        if($validator->fails()){
            return $this->ErrorResponse(400,$validator->errors()->first());
        }
        $ad = Advertisement::find($request->ads_id);
        if (empty($ad)) {
            return $this->ErrorResponse(400, 'Ad not found');
        }
        $item= RentItem::create([
            'ads_id'=>$request->ads_id,
            'user_id'=>auth()->id(),
            'rent_type'=>$request->rent_type,
            'start'=>date('y-m-d h:m:s',strtotime($request->start)),
            'end' =>date('y-m-d h:m:s',strtotime($request->end)),
            'price'=>$request->price,
            'block'=> $ad['deposit_amount'],
            'description'=>$request->description,
            'purpose'=>$request->purpose
        ]);
        return $this->SuccessResponse(200,'Rent Request send successfully',$item);

    }
    public function rentItemList(){
        $rentitem= RentItem::with('ads','wants')->latest()->get();
        return $this->SuccessResponse(200, 'Rent list Fetched Successfully', $rentitem);
    }
}
