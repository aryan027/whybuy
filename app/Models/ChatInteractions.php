<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(array $array)
 */
class ChatInteractions extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function ownerInfo() {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function userInfo() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function chats() {
        return $this->hasMany(ChatMessages::class, 'chat_id', 'id');
    }
    public function ads(){
        return $this->belongsTo(Advertisement::class,'advertisement_id');
    }
}
