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
    const IS_PUBLISHED = 1;

    protected $guarded = ['id'];
    protected $table = 'advertisements';

    protected $hidden = [
        'currency'
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    public function subCategory() {
        return $this->belongsTo(SubCategory::class, 'sub_category', 'id');
    }

    public function getCategory() {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    public function getSubCategory() {
        return $this->belongsTo(SubCategory::class, 'sub_category', 'id');
    }

    public function getUser() {
        return $this->belongsTo(User::class, 'user_id', 'id');
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
