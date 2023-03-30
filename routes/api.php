<?php

use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\RentItemController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CMSController;
use App\Http\Controllers\Api\NotificationController;
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
Route::post('register/email',[AuthController::class,'register_with_email']);
Route::post('verify',[AuthController::class,'verify_otp']);
Route::post('login',[AuthController::class,'login']);
Route::get('authentication',[AuthController::class,'authentication'])->name('authentication');
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'category'], function () {
        Route::get('/', [CategoryController::class, 'listing']);
        Route::get('category-detail', [CategoryController::class, 'categoryInformation']);
    });
    Route::group(['prefix' => 'sub-category'], function () {
        Route::get('/', [SubCategoryController::class, 'listing']);
        Route::get('/category', [SubCategoryController::class, 'subCategoriesById']);
        Route::get('/information/detail', [SubCategoryController::class, 'subCategoryInfo']);
    });
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'ads'], function () {
        Route::get('/', [AdController::class, 'adsListing']);
        Route::post('advertisement-detail', [AdController::class, 'AdvertisementDetail']);
        Route::post('owner-profile', [AdController::class, 'ownerProfile']);
        Route::post('/by/subcategory', [AdController::class, 'AdvertisementBySub']);
        Route::get('recent-search', [AdController::class, 'recentSearch']);
        Route::get('trending', [AdController::class, 'trending']);
    });
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'my-ads'], function () {
        Route::post('/create', [AdController::class, 'createAdvertisement']);
        Route::post('/update', [AdController::class, 'updateAdvertisement']);
        Route::get('/', [AdController::class, 'myAds']);
        Route::post('delete-ads', [AdController::class, 'deleteAds']);

    });
    Route::post('review',[\App\Http\Controllers\Api\ReviewController::class,'review']);
    Route::post('review/edit',[\App\Http\Controllers\Api\ReviewController::class,'review_edit']);
    Route::post('review/list',[\App\Http\Controllers\Api\ReviewController::class,'review_list']);
    Route::get('review/list/user',[\App\Http\Controllers\Api\ReviewController::class,'review_user_list']);

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
       Route::post('/item/agreement-form',[RentItemController::class,'agreementForm']);
       Route::post('/item/owner-accept-agreement',[RentItemController::class,'ownerAcceptAgreement']);
       Route::post('/item/user-confirm-agreement',[RentItemController::class,'userConfirmAgreement']);
       Route::post('/item/invoice',[RentItemController::class,'invoice']);
       Route::post('/item/cancel-item',[RentItemController::class,'cancelItem']);
       Route::get('/item/given-taken',[RentItemController::class,'givenTaken']);
       Route::post('/item/completed-by-user',[RentItemController::class,'completedByUser']);
       Route::post('/item/completed-by-owner',[RentItemController::class,'completedByOwner']);

    });
    Route::get('packages',[PackageController::class,'package_listing']);
    Route::post('subscribe',[PackageController::class,'subscription']);
    Route::get('subscribe/list',[PackageController::class,'subscriptionList']);
    Route::post('add/favourite',[FavouriteController::class,'addToFavourite']);
    Route::get('favourite/list',[FavouriteController::class,'myFavouriteList']);
    Route::post('favourite/remove',[FavouriteController::class,'myFavouriteRemove']);
    Route::group(['prefix' => 'advertisement/chat'], function () {
        Route::post('initiate', [ChatController::class, 'initiateChat']);
        Route::post('send', [ChatController::class, 'sendMessage']);
        Route::post('{cid}/delete', [ChatController::class, 'deleteChat']);
        Route::post('delete/all', [ChatController::class, 'deleteAllChat']);
        Route::post('list/aid', [ChatController::class, 'adChatList']);
        Route::get('user/list', [ChatController::class, 'listingOfUser']);
    });

    // Users Details
    Route::group(['prefix' => 'users'], function () {
        Route::post('update-password', [UserController::class, 'updatePassword']);
        Route::get('user-detail', [UserController::class, 'userDetail']);
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::post('update-profile-picture', [UserController::class, 'updateProfilePicture']);
        Route::post('update-device-token', [UserController::class, 'updateDeviceToken']);

        //Address module
        Route::post('add-address', [UserController::class, 'addAddress']);
        Route::get('get-address', [UserController::class, 'getAddress']);
        Route::post('get-address-detail', [UserController::class, 'getAddressDetail']);
        Route::post('update-address', [UserController::class, 'updateAddress']);
        Route::post('delete-address', [UserController::class, 'deleteAddress']);

        Route::post('user-report', [UserController::class, 'userReport']);

        Route::delete('delete-account', [UserController::class, 'deleteAccount']);
        Route::get('country-list', [UserController::class, 'countryList']);
        Route::post('whatsapp-message', [UserController::class, 'whatsappMessage']);
    });

    // CMS Modules
    Route::group(['prefix' => 'cms'], function () {
        // CMS
        Route::get('get-cms', [CMSController::class, 'cms']);
    });

    Route::group(['prefix' => 'forget'], function () {
        Route::post('/password/create',[AuthController::class,'password_create']);
    });

    Route::group(['prefix' => 'notification'], function () {
        // CMS
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/read-notification', [NotificationController::class, 'readNotification']);
    });
});
Route::group(['prefix' => 'forget'], function () {
    Route::post('/password',[AuthController::class,'forget_password']);
    Route::post('/verify',[AuthController::class,'verify']);
    Route::post('/password/create',[AuthController::class,'password_create']);
});

Route::get('about-us',[AboutUsController::class,'AboutUs']);
Route::get('authentication',[AuthController::class,'authentication'])->name('authentication');
Route::get('login/google', [LoginController::class,'redirectToGoogle']);
Route::get('login/google/callback', [LoginController::class,'handleGoogleCallback']);
Route::post('google/login',[AuthController::class,'login_with_google']);
