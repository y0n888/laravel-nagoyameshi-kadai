<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController as UserMembersController;
use App\Http\Controllers\RestaurantController as RestaurantMembersController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CompanyController as UserCompanyController;
use App\Http\Controllers\TermController as UserTermController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;


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
//     return view('welcome');
// });

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

    Route::get('/company', [UserCompanyController::class, 'index'])->name('company.index');

    Route::get('/terms', [UserTermController::class, 'index'])->name('terms.index');
});

Route::get('subscription/create', [SubscriptionController::class, 'create'])
    ->middleware(['auth', 'verified', 'guest:admin', NotSubscribed::class])->name('subscription.create');

Route::post('subscription', [SubscriptionController::class, 'store'])
    ->middleware(['auth', 'verified', 'guest:admin', NotSubscribed::class])->name('subscription.store');

Route::get('subscription/edit', [SubscriptionController::class, 'edit'])
    ->middleware(['auth', 'verified', 'guest:admin', Subscribed::class])->name('subscription.edit');

Route::patch('subscription', [SubscriptionController::class, 'update'])
    ->middleware(['auth', 'verified', 'guest:admin', Subscribed::class])->name('subscription.update');

Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])
    ->middleware(['auth', 'verified', 'guest:admin', Subscribed::class])->name('subscription.cancel');

Route::delete('subscription', [SubscriptionController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'guest:admin', Subscribed::class])->name('subscription.destroy');

Route::group(['middleware' => ['auth', 'verified', 'guest:admin']], function () {
    Route::get('/restaurants/{restaurant}/reviews', [ReviewController::class, 'index'])
        ->name('restaurants.reviews.index');

    Route::get('/restaurants/{restaurant}/reviews/create', [ReviewController::class, 'create'])
        ->middleware(Subscribed::class)
        ->name('restaurants.reviews.create');

    Route::post('/restaurants/{restaurant}/reviews', [ReviewController::class, 'store'])
        ->middleware(Subscribed::class)    
        ->name('restaurants.reviews.store');
        
    Route::get('/restaurants/{restaurant}/reviews/{review}/edit', [ReviewController::class, 'edit'])
        ->middleware(Subscribed::class)
        ->name('restaurants.reviews.edit');
        
    Route::put('/restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'update'])
        ->middleware(Subscribed::class)
        ->name('restaurants.reviews.update');

    Route::delete('/restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'destroy'])
        ->middleware(Subscribed::class)
        ->name('restaurants.reviews.destroy'); 
});

Route::group(['middleware' => ['auth', 'verified', 'guest:admin', Subscribed::class]], function () {
    Route::get('/reservations', [ReservationController::class, 'index'])
    ->name('reservations.index');

    Route::get('/restaurants/{restaurant}/reservations/create', [ReservationController::class, 'create'])
    ->name('restaurants.reservations.create');

    Route::post('/restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])
    ->name('restaurants.reservations.store');

    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])
    ->name('reservations.destroy');
});