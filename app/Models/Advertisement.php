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

    protected $hidden = [
        'currency'
    ];

    protected $guarded = ['id'];

    public function category() {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    public function subCategory() {
        return $this->belongsTo(SubCategory::class, 'sub_category', 'id');
    }
}
