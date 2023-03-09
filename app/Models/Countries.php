<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    use HasFactory;

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setShortNameAttribute($value)
    {
        $this->attributes['short_name'] = strtoupper($value);
    }   

    /**
     * @return HasMany
     */
    public function getAddress()
    {
        return $this->hasMany(Addresses::class, 'country_id', 'id');
    }

    // Check sub category avilable or not
    public static function checkAddressesOrNot($countries){
        $data = $countries->getAddress->pluck('id')->contains(function ($val) {
            return true;
        });
        return $data;
    }
    
}
