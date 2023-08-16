<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BookController extends Controller
{
  //adding product
  public function addBook(Request $request){

    $bookValidation = Validator::make($request->all(), [
        'title' => 'required',
        'author' => 'required',
        'description' => 'required',
        'price' => 'required',
        'quantity'=>'required'
    ]);

    if($bookValidation->fails()){
        return response()->json(['success'=>false,'message'=>'you have unenterd some required fields']);
    }

    $bookData= $bookValidation->validated();
    $book = Book::create([
        'title' => $bookData['title'],
        'author' => $bookData['author'],
        'description'=>$bookData['description'],
        'price'=>$bookData['price'],
        'quantity'=>$bookData['quantity']
    ]);

    return response()->json(['success'=>true , 'message'=>'books registered the user',$book],200);
  }

  // delete book

  public function deleteBook($id){
    $book = Book::find($id);
    if(!$book){
        return response()->json(['success'=>false , 'message'=>'no book found'],404);
    }
    $book->delete();
    return response()->json(['success'=>true , 'message'=>'successfully deleted the book']);
  }

  // update book

  public function updateBook(Request $request , $id){
    $book = Book::find($id);
    if(!$book){
        return response()->json(['success'=>false,'message'=>'book doesnot found'], 404);
    }
    $book->update($request->all());
    return response()->json(['success'=>true,'updatedBooks'=>$book], 200);
  }

  // feteching all books record

  public function getAllBooks(){
    $book = Book::all();
    if(!$book){
        return response()->json(['success'=>false , 'message'=>'no book available'],400);
    }
    return response()->json(['success'=>true , 'message'=>'successfully fetched the books' , $book],200);
  }

  // getting the record of a single book

  public function getSingleBook($id){
    $book = Book::find($id);
    if(!$book){
        return response()->json(['success'=>false , 'message'=>'No book found']);
    }
    return response()->json(['success'=>true , 'message' => 'successfully fetched the single product' , $book],200);
  }

  // searching the books

  public function searchingBookNameAndDes($keyword){
    $bookRes = Book::where('title' , 'like' , '%' .$keyword. '%')->orWhere('description','like' , '%'.$keyword.'%')->get();
    if(!$bookRes){
        return response()->json(['success'=>false , 'message'=>'book not found'],404);
    }
    return response()->json(['success'=>true ,'message'=>'successfull search the book' ,'data'=>$bookRes],200);
  }
}