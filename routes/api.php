<?php

use App\Http\Controllers\{ProductController, AuthController};
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

Route::middleware('auth:api')->prefix('products')->group(function () {
    Route::get('/getProducts', [ProductController::class, 'index'])->middleware('auth:api'); // Endpoint to fetch all products
    Route::post('/saveProducts', [ProductController::class, 'store']); // Endpoint to create a new product
    Route::get('/showProduct', [ProductController::class, 'show']); // Endpoint to fetch a specific product
    Route::post('/updateProduct', [ProductController::class, 'update']); // Endpoint to update a product
    Route::delete('/deleteProduct', [ProductController::class, 'destroy']); // Endpoint to delete a product
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
