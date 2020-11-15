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

Route::prefix('/admin')->name('admin.')->middleware('admin')->group(function (){
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'admin'])->name('home');

    Route::get('/couriers', [App\Http\Controllers\CourierController::class, 'index'])->name('couriers');
    Route::get('/courier/', [App\Http\Controllers\CourierController::class, 'create'])->name('courier.create');
    Route::post('/courier/', [App\Http\Controllers\CourierController::class, 'store'])->name('courier.store');
    Route::get('/courier/{id}', [App\Http\Controllers\CourierController::class, 'show'])->name('courier.show');
    Route::post('/courier/{id}', [App\Http\Controllers\CourierController::class, 'update'])->name('courier.update');
    Route::patch('/courier/{id}', [App\Http\Controllers\CourierController::class, 'updatePass'])->name('courier.update.pass');
    Route::delete('/courier/{id}', [App\Http\Controllers\CourierController::class, 'delete'])->name('courier.delete');
});
