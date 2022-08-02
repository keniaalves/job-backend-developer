<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiProductsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('products', [ApiProductsController::class, 'getAllProducts']);
Route::get('product/{id?}', [ApiProductsController::class, 'getProduct']);
Route::post('product', [ApiProductsController::class, 'createProduct']);
Route::put('product/{id}', [ApiProductsController::class, 'updateProduct']);
Route::delete('product/{id}', [ApiProductsController::class, 'deleteProduct']);
Route::get('products/category/{category}', [ApiProductsController::class, 'getProductsByCategory']);
