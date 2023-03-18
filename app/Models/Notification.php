<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    const IS_READ = 1;
    const IS_UNREAD = 0;
    protected $guarded= ['id'];
    protected $table = 'notification';

    public function getRentItem(){
        return $this->belongsTo(RentItem::class,'rent_item_id');
    }

    public function getSenderUser(){
        return $this->belongsTo(User::class,'sender_id');
    }
}
