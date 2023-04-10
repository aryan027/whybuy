<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RentItem;
use App\Models\CMS;
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

    // Privacy Policy
    public function privacyPolicy(Request $request)
    {
        $privacyPolicy = CMS::where('type','privacy_policy')->first();
        return view('cms.privacy-policy',compact('privacyPolicy'));
            
    }

    //Terms & Condition
    public function termsCondition(Request $request)
    {
        $termsCondition = CMS::where('type','terms_condition')->first();
        return view('cms.terms-condition',compact('termsCondition'));
            
    }

    
}
