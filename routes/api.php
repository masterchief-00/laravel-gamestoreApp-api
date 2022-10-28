<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishListController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/users/signup', [UserController::class, 'signup']);
Route::post('/users/login', [UserController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/users/logout', [UserController::class, 'logout']);
    Route::put('/users/{email}',[UserController::class,'update']);
    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/games/{id}', [GameController::class, 'show']);
    Route::put('/games/{id}', [GameController::class, 'update']);
    Route::delete('/games/{id}', [GameController::class, 'destroy']);
    Route::post('/users/delete_account', [UserController::class, 'destroy']);
    Route::post('/wishlist', [WishListController::class, 'store']);
    Route::get('/wishlist', [WishListController::class, 'index']);
    Route::get('/wishlist/{id}', [WishListController::class, 'show']);
    Route::delete('/wishlist/{id}', [WishListController::class, 'destroy']);
});
