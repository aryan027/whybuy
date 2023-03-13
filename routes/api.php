<?php

use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\RentItemController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CMSController;
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

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'ads'], function () {
        Route::get('/', [AdController::class, 'adsListing']);
        Route::post('advertisement-detail', [AdController::class, 'AdvertisementDetail']);
        Route::post('owner-profile', [AdController::class, 'ownerProfile']);
    });
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'my-ads'], function () {
        Route::post('/create', [AdController::class, 'createAdvertisement']);
        Route::post('/update', [AdController::class, 'updateAdvertisement']);
        Route::get('/', [AdController::class, 'myAds']);
        Route::post('/published', [AdController::class, 'published']);
    });

    Route::group(['prefix' =>'wallet'],function (){
        Route::get('/',[\App\Http\Controllers\Api\WalletController::class,'wallet_details']);
        Route::post('/ad/rent',[\App\Http\Controllers\Api\WalletController::class,'wallet_transaction']);
        Route::post('/amount/release',[\App\Http\Controllers\Api\WalletController::class,'release_product_value']);
        Route::post('/add/money',[\App\Http\Controllers\Api\WalletController::class,'add_balance']);
        Route::post('/create',[\App\Http\Controllers\Api\WalletController::class,'create_wallet']);
        Route::get('product/wise/history',[\App\Http\Controllers\Api\WalletController::class,'product_wise_wallet_history']);
        Route::get('money/history',[\App\Http\Controllers\Api\WalletController::class,'add_money_history']);
    });
       Route::group(['prefix'=>'rent'],function(){
       Route::Post('/item',[RentItemController::class,'rentItem']) ;
       Route::get('/item/rent-detail',[RentItemController::class,'rentDetail']);
       Route::post('/timeslot',[RentItemController::class,'timeSlot']) ;
       Route::get('/item/list',[RentItemController::class,'myRentItemList']);
       Route::get('/item/rent-provide-list',[RentItemController::class,'provideRentItemList']);
       Route::get('/item/agreement-form',[RentItemController::class,'agreementForm']);
       Route::post('/item/owner-confirm-agreement',[RentItemController::class,'ownerConfirmAgreement']);
       Route::post('/item/user-accept-agreement',[RentItemController::class,'userAcceptAgreement']);
       
    });
    Route::get('packages',[PackageController::class,'package_listing']);
    Route::post('add/favourite',[FavouriteController::class,'addToFavourite']);
    Route::get('favourite/list',[FavouriteController::class,'myFavouriteList']);
    Route::group(['prefix' => 'advertisement/chat'], function () {
        Route::post('initiate', [ChatController::class, 'initiateChat']);
        Route::post('{cid}', [ChatController::class, 'sendMessage']);
        Route::post('{cid}/delete', [ChatController::class, 'deleteChat']);
        Route::post('delete/all', [ChatController::class, 'deleteAllChat']);
        Route::get('list/{aid}', [ChatController::class, 'adChatList']);
        Route::get('user/list', [ChatController::class, 'listingOfUser']);
    });

    // Users Details
    Route::group(['prefix' => 'users'], function () {
        Route::get('user-detail', [UserController::class, 'userDetail']);
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::post('update-profile-picture', [UserController::class, 'updateProfilePicture']);
        
        //Address module 
        Route::post('add-address', [UserController::class, 'addAddress']);
        Route::get('get-address', [UserController::class, 'getAddress']);
        Route::get('get-address-detail/{id}', [UserController::class, 'getAddressDetail']);
        Route::put('update-address/{id}', [UserController::class, 'updateAddress']);
        Route::delete('delete-address/{id}', [UserController::class, 'deleteAddress']);

        Route::post('user-report', [UserController::class, 'userReport']);

        Route::delete('delete-account', [UserController::class, 'deleteAccount']);
    });

    // CMS Modules
    Route::group(['prefix' => 'cms'], function () {
        // CMS
        Route::get('get-cms', [CMSController::class, 'cms']);
    });
});

Route::get('about-us',[AboutUsController::class,'AboutUs']);
Route::get('authentication',[AuthController::class,'authentication'])->name('authentication');
