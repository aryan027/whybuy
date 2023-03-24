<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentSearchAds extends Model
{
    use HasFactory;
    protected $guarded =['id'];
    protected $table = 'recent_search_ads';

    public function recentSearchAds(){
        return $this->belongsTo(Advertisement::class,'ad_id');
    }
}
