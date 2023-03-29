<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentItem extends Model
{
    use HasFactory;
    protected $guarded =['id'];

    public function ads(){
        return $this->belongsTo(Advertisement::class,'ads_id');
    }

    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function owners(){
        return $this->belongsTo(User::class,'owner_id');
    }

    public function rentAddress(){
        return $this->belongsTo(Addresses::class,'address_id');
    }

}
