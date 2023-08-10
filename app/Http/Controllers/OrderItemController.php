<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Events\UpdateOrderTotal;
use App\Models\Order;

class OrderItemController extends Controller
{
    // adding order item
    public function addOrderItem(Request $request , $orderId){
        $orderItVal = Validator::make($request->all(), [
            'book_id' => 'required',
            'quantity' => 'required',
        ]);
        if ($orderItVal->fails()) {
            return response()->json(['success' => false, 'message' => $orderItVal->errors()], 400);
        }
        $validatedData = $orderItVal->validated();

        $book = Book::find($validatedData['book_id']);
        $order = Order::find($orderId);

        if(!$book){
            return response()->json(['success'=>false , 'message'=>'book not found'],404);
        }
        if(!$order){
            return response()->json(['success'=>false , 'message'=>'order not found']);
        }

        if($book->quantity < $validatedData['quantity']){
            return response()->json(['success'=>false ,'message'=>'out of stock'],400);
        }

        $subtotal = $book->price * $validatedData['quantity'];

        $orderitem = OrderItem::create([
            'o_id'=>$order->id,
            'book_id'=>$book->id,
            'quantity'=>$validatedData['quantity'],
            'subtotal'=>$subtotal
        ]);

        $book->decrement('quantity', $validatedData['quantity']);
        event(new UpdateOrderTotal($order));
        return response()->json(['success'=>true , $orderitem],200);
    }
}
