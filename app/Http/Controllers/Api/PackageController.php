<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PackageController extends Controller
{
    protected $packages;
     public function __construct()
     {
         $this->packages= Package::where(['status'=>true])->get();
     }
    public function package_listing() {
        try {
            return $this->SuccessResponse(200, 'Packages Fetched', $this->packages);
        } catch (Exception $exception) {
            logger('error occurred in Packages fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    public function subscription(Request $request)
    {
        $package = Package::find($request->package_id);
        if(empty($package)){
            return $this->ErrorResponse(200,'Package not found');
        }
      $subs=   Subscription::create([
            'package_id'=> $request->package_id,
            'user_id'=>auth()->id(),
            'start'=>Carbon::now(),
            'end' =>Carbon::now()->addDays($package->durations),
            'no_of_ads'=> $package->no_of_ads,
            'price'=> $package->price,
          'type'=>$package->type
            ]);
        if($subs){
            return $this->SuccessResponse(200,'subscription fetch successfully ..!',$subs);
        }
        return $this->ErrorResponse(200,'something went wrong ..!');
    }

    public function  subscriptionList(){
         $sub= Subscription::where('user_id',auth()->id())->latest()->get();
         return $this->SuccessResponse('200','Data fetch successfully ..',$sub);
    }

}
