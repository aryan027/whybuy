<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(true[] $array)
 */
class Category extends Model
{
    use HasFactory,SoftDeletes;

    const IS_ACTIVE = 1;
    const IS_INACTIVE = 2;
    
    protected $guarded = ['id'];
    protected $table = 'categories';

    /**
     * @return HasMany
     */
    public function subCategory(): HasMany
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function childCategory(): HasMany
    {
        return $this->hasMany(ChildCategory::class, 'category_id', 'id');
    }

    // Check sub category avilable or not
    public static function checkSubCategoryOrNot($category){
        $data = $category->subCategory->pluck('id')->contains(function ($val) {
            return true;
        });
        return $data;
    }
}
