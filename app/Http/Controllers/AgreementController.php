<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RentItem;
use App\Models\User;

class AgreementController extends Controller
{
    public function agreementForm(Request $request,$rent_item_id,$user_id)
    {
        if($rent_item_id && $user_id){
            $user = User::where('id',$user_id)->first();
            $rentItem = RentItem::where('id',$rent_item_id)->first();
            if(!empty($rentItem) && $user){
                return view('agreement.form',compact('user','rentItem'));
            }
        }
    }
}
