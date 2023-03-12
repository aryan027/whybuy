<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
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
}
