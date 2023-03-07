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

    public function wants(){
        return $this->belongsTo(User::class,'user_id');
    }

}
