<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::post('/createOrUpdateProduct', [App\Http\Controllers\ProductController::class, "createOrUpdateProduct"])->middleware('checkaccess');
Route::get('/listProducts', [App\Http\Controllers\ProductController::class, "listProducts"]);
Route::get('/getProduct/{productId}', [App\Http\Controllers\ProductController::class, "getProduct"]);
Route::delete('/deleteProduct/{productId}', [App\Http\Controllers\ProductController::class, "deleteProduct"]);
