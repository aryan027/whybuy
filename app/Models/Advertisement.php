<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @method static create(array $all)
 */
class Advertisement extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $hidden = [
        'currency'
    ];

    protected $guarded = ['id'];


    public function category() {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    public function sub_category() {
        return $this->belongsTo(SubCategory::class, 'sub_category', 'id');
    }
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10);

    }
    public function getImageAttribute()
    {
        return $this->getMedia() ;
    }
}
