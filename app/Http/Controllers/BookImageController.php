<?php

namespace App\Http\Controllers;

use App\Models\BookImage;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class BookImageController extends Controller
{
    // adding book images

    public function addBookImages(Request $request , $bookId){

        $book = Book::findOrFail($bookId);

        if(!$book){
            return response()->json(['success'=>false , 'message'=>'book doesnot exists'],404);
        }
        $imageValidation = Validator::make($request->all(), [
            'image'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if($imageValidation->fails()){
            return response()->json(['success'=>false , 'errors'=>$imageValidation->errors()],400);
        }

        $validatedImages = $request->file('image');

        $imagePath = $validatedImages->store('book_images','public');

        $imageRec= BookImage::create([
            'book_id'=>$book->id,
            'image_url'=>$imagePath
        ]);

        return response()->json(['success'=>true, 'imageData'=>$imageRec],200);
    }

    // adding the book images

    public function getBookImages($bookId){

        $book = Book::findOrFail($bookId);
        if(!$book){
            return response()->json(['success'=>false , 'message'=>'book doesnot exists'],404);
        }
        $images = $book->bookImages;
        return response()->json(['success'=>true,'bookImages'=>$images],200);
    }

    // delete Image

    public function deleteBookImage($imageId){

        $image = BookImage::findOrFail($imageId);
        if(!$image){
            return response()->json(['success'=>false , 'message'=>'book image doesnot exists'],404);
        }
        Storage::delete('public/' . $image->image_url);
        $image->delete();
        return response()->json(['success'=>true,'message'=>'successfully deleted the book image'],200);
    }

    // update image

    public function updateBookImage(Request $request,$imageId){

        $image = BookImage::findOrFail($imageId);
        if(!$image){
            return response()->json(['success'=>false , 'message'=>'book image doesnot exists'],404);
        }
        $imageValidation = Validator::make($request->all(), [
            'image'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if($imageValidation->fails()){
            return response()->json(['success'=>false , 'error'=>$imageValidation->errors()],400);
        }
        Storage::delete('public/' . $image->image_url);

        $uploadedImage = $request->file('image');
        //dd($uploadedImage);
        $newImagePath = $uploadedImage->store('book_images', 'public');

        $image->update([
            'image_url' => $newImagePath,
        ]);
        return response()->json(['success' => true, 'message' => 'Image updated successfully', 'image' => $image], 200);
    }
}