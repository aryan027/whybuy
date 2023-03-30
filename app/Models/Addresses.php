<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Addresses extends Model
{
    use HasFactory,SoftDeletes;

    public function rendAddress(){
        return $this->hasMany(RentItem::class,'address_id');
    }
}
