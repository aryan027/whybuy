<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(array $array)
 */
class SubCategory extends Model
{
    use HasFactory,SoftDeletes;

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
    
}
