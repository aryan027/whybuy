<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ChildCategoryController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CMSController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\AgreementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     // return view('welcome');
//     return view('auth.login');
// });
Route::get('/agreement-form/{rent_item_id}/{user_id}', [AgreementController::class, 'agreementForm']);
Route::get('/privacy-policy', [AgreementController::class, 'privacyPolicy']);
Route::get('/terms-condition', [AgreementController::class, 'termsCondition']);

Route::get('/', [App\Http\Controllers\HomeController::class, 'login']);
Route::get('/admin', [App\Http\Controllers\HomeController::class, 'login']);

// Route::get('/admin', function () {
//     // return view('welcome');
//     return view('auth.login');
// });

Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        // Countries
        Route::resource('category', CategoryController::class);

        // Sub category
        Route::resource('sub-category', SubCategoryController::class);

        // Child category
        Route::resource('child-category', ChildCategoryController::class);

        // Advertisement module
        Route::resource('advertisement', AdvertisementController::class);
        Route::put('advertisement-approve/{id}', [AdvertisementController::class,'advertisementApprove'])->name('advertisement.approve');      
        
        //Country  module
        Route::resource('country', CountryController::class);

        // terms & condition or privercy policy  module
        Route::resource('cms', CMSController::class);
       
        // users module
        Route::resource('user', UserController::class);
        Route::put('user-status/{id}', [UserController::class,'userStatus'])->name('user.status');      

        // Subscription module
        Route::resource('package', SubscriptionController::class);
    });
});
