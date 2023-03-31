<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentItem extends Model
{
    use HasFactory;
    const IS_PENDING = 0;
    const IS_APPROVED = 1;
    const IS_CONFIRM = 2;
    const IS_CANCEL = 3;
    const IS_COMPLETED_BY_USER = 4;
    const IS_COMPLETED_BY_OWNER = 5;
    const IS_SUBMITED_BY_USER = 6;

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

    public function getRentTransaction(){
        return $this->hasOne(TransactionHistory::class,'rent_id');
    }

}
