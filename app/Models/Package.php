<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    protected $guarded= ['id'];

     /**
     * @return HasMany
     */
    public function getSubscription()
    {
        return $this->hasMany(Subscription::class, 'package_id', 'id');
    }

    // Check Subscription avilable or not
    public static function checkPackageOrNot($package){
        $data = $package->getSubscription->pluck('id')->contains(function ($val) {
            return true;
        });
        return $data;
    }

}
