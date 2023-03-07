<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected array|Collection $categories;

    public function __construct() {
        $this->categories = Category::where(['status' => true])->get()->map(function ($category){
            $category->images= $category->image;
            unset($category['media']);
            return $category;

        });

    }

    public function listing() {
        try {
            return $this->SuccessResponse(200, 'Categories Fetched', $this->categories);
        } catch (Exception $exception) {
            logger('error occurred in categories fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    public function categoryInformation($cid) {
        try {
            $category = $this->categories->where('id', $cid)->first();
            $category['image']= $category->getFirstMediaUrl('category','thumb') ;
            unset($category['media']);
            if (!$category) {
                return $this->ErrorResponse(404, 'Category not found');
            }
            return $this->SuccessResponse(200, 'Category Fetched Successfully', $category);
        } catch (Exception $exception) {
            logger('error occurred in categories fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }
}
