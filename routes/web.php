<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController as UserMembersController;
use App\Http\Controllers\RestaurantController as RestaurantMembersController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;


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

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');

    Route::resource('users', Admin\UserController::class)->only(['index', 'show']);

    Route::resource('restaurants',  Admin\RestaurantController::class);

    Route::resource('categories',  Admin\CategoryController::class);

    Route::resource('company',  Admin\CompanyController::class);

    Route::resource('terms',  Admin\TermController::class);

    
});

Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::resource('user', UserMembersController::class)->only(['index', 'edit', 'update']);
    });

    Route::resource('restaurants', RestaurantMembersController::class)->only(['index', 'show']);
});

