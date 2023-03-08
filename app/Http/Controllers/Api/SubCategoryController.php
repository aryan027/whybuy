<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubCategoryController extends Controller
{
    protected array|Collection $subCategories;

    public function __construct() {
        $this->subCategories = SubCategory::with('category')->where(['status' => true])->get()->map(function ($sub){
            $sub->images= $sub->image;
            unset($sub['media']);
            return $sub;
        });
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|JsonResponse|Response
     */
    public function listing() {
        try {
            return $this->SuccessResponse(200, 'Sub Categories Fetched', $this->subCategories);
        } catch (Exception $exception) {
            logger('error occurred in sub categories fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    /**
     * @param $cid
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|JsonResponse|Response
     */
    public function subCategoriesById($cid) {
        try {
            $subCategories = $this->subCategories->where('category_id', $cid);

            return $this->SuccessResponse(200, 'Sub Categories Fetched', $subCategories);
        } catch (Exception $exception) {
            logger('error occurred in sub categories find by id process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    /**
     * @param $sid
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|JsonResponse|Response
     */
    public function subCategoryInfo($sid) {
        try {
            $subCategory = $this->subCategories->where('id', $sid)->first();

            if (!$subCategory) {
                return $this->ErrorResponse(404, 'Sub Category not found');
            }
            return $this->SuccessResponse(200, 'Sub Categories Fetched', $subCategory);
        } catch (Exception $exception) {
            logger('error occurred in sub categories find by id process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }
}
