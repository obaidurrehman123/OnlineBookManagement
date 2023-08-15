<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
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

// miidleware authorized + admin
Route::middleware(['auth.api','admin.user'])->group(function () {

    //book protected routes
    Route::post('/createBook',[BookController::class,'addBook']);
    Route::delete('/deleteBook/{id}',[BookController::class,'deleteBook']);
    Route::put('/updateBook/{id}',[BookController::class,'updateBook']);

    //category protected routes
    Route::post('/createCategory',[CategoryController::class,'addCategory']);
    Route::delete('/deleteCategory/{id}',[CategoryController::class,'deleteCategory']);
    Route::put('/updateCategory/{id}',[CategoryController::class,'updateCategory']);

    // get All Order Details
    Route::get('/getAllOrders',[OrderController::class,'showAllOrders']);
    Route::put('/updateStatus/{id}',[OrderController::class,'updateOrderStatus']);
    Route::get('/getUnshipped/{id}',[OrderController::class,'getUnshippedOrderDetails']);
});

// middleware authorized
Route::middleware(['auth.api'])->group(function(){
    //order routes
    Route::post('/createUserOrd' , [OrderController::class , 'createOrder']);
    //orderitem routes
    Route::post('/addOrderItems/{id}',[OrderItemController::class , 'addOrderItem']);
    Route::get('/getorderItems/{id}',[OrderItemController::class ,'getOrderItems']);
    Route::delete('/deleteOrderItem/{id}',[OrderItemController::class,'removeItems']);
    Route::put('/updateOrderItems/{id}',[OrderItemController::class,'updateOrderItems']);
});


//book public routes
Route::get('/getAllbooks',[BookController::class,'getAllBooks']);
Route::get('/searchProduct/{keyword}',[BookController::class,'searchingBookNameAndDes']);

//category public routes
Route::get('/getAllCat' , [CategoryController::class,'getAllCategories']);
Route::get('/getSingleCat/{id}' , [CategoryController::class , 'getSingleCategory']);

// user routes
Route::post('/createUser',[UserController::class,'signUp']);
Route::post('/logInUser' , [UserController::class,'signIn']);