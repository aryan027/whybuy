<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PackageController extends Controller
{
    protected  array|Collection  $packages;
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
}
