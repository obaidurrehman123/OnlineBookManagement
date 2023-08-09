<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function addCategory(Request $request){
        $categoryValidation = Validator::make($request->all(), [
            'category_name' => 'required',
        ]);
        if($categoryValidation->fails()){
            return response()->json(['success'=>false,'message'=>'you have unenterd some required fields']);
        }
        $validatedCategory = $categoryValidation->validated();

        $category = Category::create([
            'category_name'=>$validatedCategory['category_name']
        ]);
        return response()->json(['success'=>true , 'message'=>'successfully created the category' , 'data' => $category],200);
    }

    public function deleteCategory($id){
        $category= Category::find($id);
        if(!$category){
            return response()->json(['success'=>false , 'message'=>'category not found'],404);
        }
        $category->delete();
        return response()->json(['success'=>true , 'message'=>'successfully deleted the category'],200);
    }

    public function updateCategory(Request $request , $id){
        $category= Category::find($id);
        if(!$category){
            return response()->json(['success'=>false , 'message'=>'category not found'],404);
        }
        $category->update($request->all());
        return response()->json(['success'=>true,'updatedCategory'=>$category], 200);
    }

    public function getAllCategories(){
        $category = Category::all();
        if(!$category){
            return response()->json(['success'=>false , 'message'=>'no category available'],404);
        }
        return response()->json(['success'=>true , 'message'=>'successfully fetched the categories' , $category],200);
      }

    public function getSingleCategory($id){
        $category= Category::find($id);
        if(!$category){
            return response()->json(['success'=>false , 'message'=>'category not found'],404);
        }
        return response()->json(['success'=>true , 'message'=>'successfully fetched the category' , $category],200);
    }
}
