<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable,InteractsWithMedia,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'password',
        'city',
        'country_id',
        'state',
        'pin',
        'address',
        'gender',
        'device_type',
        'device_token',
        'device_id',
        'latitude',
        'longitude',
        'last_login',
        'status',
        'dob',
        'mobile',
        'google_id'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' =>'datetime'
    ];

    public $appends = ['full_name'];

    public function getFullNameAttribute()
    {
        return "{$this->fname} {$this->lname}";
    }

    /** get user all advertisement
     * @return HasMany
    */
    public function getPublishedAdv()
    {
        return $this->hasMany(Advertisement::class, 'user_id', 'id');
    }

}
