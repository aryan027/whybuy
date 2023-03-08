<?php

use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\RentItemController;
use App\Http\Controllers\Api\SubCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('logout',[AuthController::class,'logout']);
});

Route::post('register',[AuthController::class,'register']);
Route::post('verify',[AuthController::class,'verify_otp']);
Route::post('login',[AuthController::class,'login']);
Route::get('authentication',[AuthController::class,'authentication'])->name('authentication');
Route::group(['prefix' => 'category'], function () {
    Route::get('/', [CategoryController::class, 'listing']);
    Route::get('/{cid}', [CategoryController::class, 'categoryInformation']);
});
Route::group(['prefix' => 'sub-category'], function () {
    Route::get('/', [SubCategoryController::class, 'listing']);
    Route::get('/{cid}', [SubCategoryController::class, 'subCategoriesById']);
    Route::get('/information/{sid}', [SubCategoryController::class, 'subCategoryInfo']);
});
Route::get('ads', [AdController::class, 'adsListing']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'my-ads'], function () {
        Route::post('/create', [AdController::class, 'createAdvertisement']);
        Route::get('/', [AdController::class, 'myAds']);
    });
    Route::group(['prefix'=>'rent'],function(){
       Route::Post('/item',[RentItemController::class,'rentItem']) ;
       Route::get('/item/list',[RentItemController::class,'rentItemList']);
    });
    Route::get('packages',[PackageController::class,'package_listing']);
    Route::post('add/favourite',[FavouriteController::class,'addToFavourite']);
    Route::post('favourite/list',[FavouriteController::class,'myFavouriteList']);
});

Route::get('about-us',[AboutUsController::class,'AboutUs']);
