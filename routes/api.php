<?php

use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Book routes
Route::middleware(['auth.api','admin.user'])->group(function () {
    Route::post('/createBook',[BookController::class,'addBook']);
    Route::delete('/deleteBook/{id}',[BookController::class,'deleteBook']);
    Route::put('/updateBook/{id}',[BookController::class,'updateBook']);
});

Route::get('/getAllbooks',[BookController::class,'getAllBooks']);
Route::get('/searchProduct/{keyword}',[BookController::class,'searchingBookNameAndDes']);

// user routes
Route::post('/createUser',[UserController::class,'signUp']);
Route::post('/logInUser' , [UserController::class,'signIn']);