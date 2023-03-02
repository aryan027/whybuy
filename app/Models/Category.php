<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static where(true[] $array)
 */
class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

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
}
