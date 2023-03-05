<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $all)
 */
class Advertisement extends Model
{
    use HasFactory;
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

}
