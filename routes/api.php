<?php

use App\Http\Controllers\Api\EnrolmentApiController;
use App\Http\Controllers\Api\MerchandiseApiDocumentationController;
use App\Http\Controllers\Api\MerchandiseApiController;
use App\Http\Controllers\Api\XenditCallBackApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('/xendit/callback', [XenditCallBackApiController::class, 'handleCallback']);
Route::post('/enrolment/post', [EnrolmentApiController::class, 'post']);
Route::prefix('v1/merchandise')->middleware('throttle:60,1')->group(function () {
    Route::get('/store', [MerchandiseApiController::class, 'store']);
    Route::get('/categories', [MerchandiseApiController::class, 'categories']);
    Route::get('/products', [MerchandiseApiController::class, 'products']);
    Route::get('/products/{productId}', [MerchandiseApiController::class, 'product'])
        ->whereNumber('productId');

    Route::post('/carts', [MerchandiseApiController::class, 'createCart']);
    Route::get('/carts', [MerchandiseApiController::class, 'cart']);
    Route::post('/carts/items', [MerchandiseApiController::class, 'addCartItem']);
    Route::put('/carts/items/{itemId}', [MerchandiseApiController::class, 'updateCartItem'])
        ->whereNumber('itemId');
    Route::delete('/carts/items/{itemId}', [MerchandiseApiController::class, 'removeCartItem'])
        ->whereNumber('itemId');
    Route::get('/carts/checkout', [MerchandiseApiController::class, 'checkout']);
    Route::post('/orders', [MerchandiseApiController::class, 'createOrder']);
});
Route::get('/v1/merchandise/openapi.json', MerchandiseApiDocumentationController::class);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
