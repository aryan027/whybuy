<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @method static where(array $array)
 */
class SubCategory extends Model implements HasMedia
{
    use HasFactory,SoftDeletes,InteractsWithMedia;

    const IS_ACTIVE = 1;
    const IS_INACTIVE = 2;
    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function childCategory(): HasMany
    {
        return $this->hasMany(ChildCategory::class, 'sub_category_id', 'id');
    }

    // Check child category avilable or not
    public static function checkChildCategoryOrNot($category){
        $data = $category->childCategory->pluck('id')->contains(function ($val) {
            return true;
        });
        return $data;
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
        return $this->getFirstMediaUrl('sub_category','thumb') ;
    }

}
