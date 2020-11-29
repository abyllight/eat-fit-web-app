<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes([
    'register' => false
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/home', [App\Http\Controllers\HomeController::class, 'sendWhatsapp'])->name('whatsapp');

Route::prefix('/admin')->name('admin.')->middleware('admin')->group(function (){
    Route::get('/home', [App\Http\Controllers\AdminController::class, 'index'])->name('home');
    Route::post('/home', [App\Http\Controllers\AdminController::class, 'setWeek'])->name('home.week');
    Route::get('/whatsapp', [App\Http\Controllers\AdminController::class, 'showWhatsapp'])->name('whatsapp');
    Route::post('/whatsapp', [App\Http\Controllers\AdminController::class, 'updateWhatsappText'])->name('whatsapp.update');

    Route::get('/couriers', [App\Http\Controllers\CourierController::class, 'index'])->name('couriers');
    Route::get('/courier/', [App\Http\Controllers\CourierController::class, 'create'])->name('courier.create');
    Route::post('/courier/', [App\Http\Controllers\CourierController::class, 'store'])->name('courier.store');
    Route::get('/courier/{id}', [App\Http\Controllers\CourierController::class, 'show'])->name('courier.show');
    Route::post('/courier/{id}', [App\Http\Controllers\CourierController::class, 'update'])->name('courier.update');
    Route::patch('/courier/{id}', [App\Http\Controllers\CourierController::class, 'updatePass'])->name('courier.update.pass');
    Route::delete('/courier/{id}', [App\Http\Controllers\CourierController::class, 'delete'])->name('courier.delete');

    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders');
    Route::get('/orders/amo', [App\Http\Controllers\OrderController::class, 'getOrders'])->name('orders.amo');

    Route::get('/geo', [App\Http\Controllers\GeoController::class, 'index'])->name('geo');
    Route::get('/geo/geocode', [App\Http\Controllers\GeoController::class, 'geocode'])->name('geocode');
    Route::get('/geo/interval', [App\Http\Controllers\GeoController::class, 'interval'])->name('interval');

    Route::get('/map', [App\Http\Controllers\MapController::class, 'index'])->name('map');
    Route::post('/map', [App\Http\Controllers\MapController::class, 'filter'])->name('map.filter');

    Route::get('/list', [App\Http\Controllers\ListController::class, 'index'])->name('list');
    Route::post('/list', [App\Http\Controllers\ListController::class, 'updateList'])->name('list.update');
    Route::get('/list/excel', [App\Http\Controllers\ListController::class, 'export'])->name('list.export');
});
