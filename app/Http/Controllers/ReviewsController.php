<?php

namespace App\Http\Controllers;

use App\Models\Reviews;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Book;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReviewsController extends Controller
{
    // adding reviews
    public function addReviews(Request $request , $bookId){

        $book = Book::findOrFail($bookId);
        $user = JWTAuth::parseToken()->authenticate();
        if(!$book){
            return response()->json(['success'=>false , 'message'=>'book doesnot exists'],404);
        }
        if(!$user){
            return response()->json(['success'=>false , 'message'=>'user doesnot exists'],404);
        }
        $reviewsVal = Validator::make($request->all(), [
            'reviews' => 'required|string',
        ]);
        if($reviewsVal->fails()){
            return response()->json(['success'=>false,'errors'=>$reviewsVal->errors()],400);
        }
        $validatedRev = $reviewsVal->validated();
        $review = Reviews::create([
            'user_id'=>$user->id,
            'book_id'=>$book->id,
            'reviews'=>$validatedRev['reviews']
        ]);

        return response()->json(['success'=>true , 'reviewdata'=>$review],200);
    }

    //getting reviews

    public function getReviews($bookId){
        $book = Book::findOrFail($bookId);
        if(!$book){
            return response()->json(['success'=>false , 'message'=>'book doesnot exists'],404);
        }
        $reviews = DB::table('reviews')
        ->join('users', 'reviews.user_id', '=', 'users.id')
        ->where('reviews.book_id', $bookId)
        ->select('reviews.id', 'reviews.user_id', 'reviews.reviews', 'users.username as user_name')
        ->get();
        return response()->json(['success' => true, 'reviews' => $reviews], 200);
    }

    // delete review

    public function deleteReview($reviewId){
        $review = Reviews::findOrFail($reviewId);
        $user = JWTAuth::parseToken()->authenticate();
        if(!$review){
            return response()->json(['success'=>false , 'message'=>'review doesnot exists'],404);
        }
        if($review->user_id !== $user->id){
            return response()->json(['success'=>false ,'message'=>'not allowed to delete the review'],400);
        }
        $review->delete();

        return response()->json(['success'=>true , 'message'=>'deleted the review successfully'],200);
    }

    //update review

    public function updateReview(Request $request,$reviewId){
        $review = Reviews::findOrFail($reviewId);
        $user = JWTAuth::parseToken()->authenticate();
        if(!$review){
            return response()->json(['success'=>false , 'message'=>'review doesnot exists'],404);
        }
        if($review->user_id !== $user->id){
            return response()->json(['success'=>false ,'message'=>'not allowed to delete the review'],400);
        }
        $reviewsVal = Validator::make($request->all(), [
            'reviews' => 'required|string',
        ]);
        if($reviewsVal->fails()){
            return response()->json(['success'=>false,'errors'=>$reviewsVal->errors()],400);
        }
        $validatedRev = $reviewsVal->validated();
        $review->update([
            'reviews'=>$validatedRev['reviews']
        ]);
        return response()->json(['success'=>true , 'message'=>'successfully updated the review' , "reviewdata"=>$review],200);
    }
}