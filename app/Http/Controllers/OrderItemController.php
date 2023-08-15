<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Events\UpdateOrderTotal;
use App\Models\Order;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;


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
        $user = JWTAuth::parseToken()->authenticate();
        $book = Book::find($validatedData['book_id']);
        $order = Order::where('u_id', $user->id)->findOrFail($orderId);

        if(!$user){
            return response()->json(['success'=>false , 'message' => 'not authoried user']);
        }

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
            'order_id'=>$order->id,
            'book_id'=>$book->id,
            'quantity'=>$validatedData['quantity'],
            'subtotal'=>$subtotal
        ]);

        $book->decrement('quantity', $validatedData['quantity']);
        event(new UpdateOrderTotal($order));
        return response()->json(['success'=>true , $orderitem],200);
    }

    // getting the items against the order

    public function getOrderItems($orderId){
        $user = JWTAuth::parseToken()->authenticate();
        if(!$user){
            return response()->json(['success'=>false , 'message' => 'not authoried user'],400);
        }
        $order = Order::where('u_id', $user->id)->findOrFail($orderId);
        if(!$order){
            return response()->json(['success'=>false , 'message'=>'order not found'],404);
        }
        $result = DB::table('books AS b')
        ->join('order_items AS oi', 'b.id', '=', 'oi.book_id')
        ->select('b.title', 'b.author', 'b.description', 'b.price', 'oi.quantity', 'oi.subtotal')
        ->get();
        return response()->json(['success'=>true , $result],200);
    }

    // Removing specific order item

    public function removeItems($orderItem){

        $user = JWTAuth::parseToken()->authenticate();
        $orderItem = OrderItem::findOrFail($orderItem);
        if($user->id !== $orderItem->order->u_id){
            return response()->json(['success'=>false , 'message'=> 'order not found'],404);
        }
        $orderItem->book->increment('quantity' , $orderItem->quantity);

        $orderItem->delete();
        event(new UpdateOrderTotal($orderItem->order));

        return response()->json(['success' => true, 'message' => 'Order item removed successfully'], 200);
    }

    // updating order item details

    public function updateOrderItems(Request $request , $orderItem){

        $user = JWTAuth::parseToken()->authenticate();
        $orderItem = OrderItem::findOrFail($orderItem);
        if($user->id !== $orderItem->order->u_id){
            return response()->json(['success'=>false , 'message'=> 'order not found']);
        }

        $updateOrdIteVal = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if($updateOrdIteVal->fails()){
            return response()->json(['success' => false, 'message' => $updateOrdIteVal->errors()], 400);
        }

        $validatedData = $updateOrdIteVal->validated();

        $newQuantity = $validatedData['quantity'];
        $newSubtotal = $orderItem->book->price * $newQuantity;

        $quantityDiff = $newQuantity - $orderItem->quantity;

        $orderItem->update([
            'quantity' => $newQuantity,
            'subtotal' => $newSubtotal,
        ]);

        $orderItem->book->decrement('quantity',abs($quantityDiff));

        event(new UpdateOrderTotal($orderItem->order));

        return response()->json(['success' => true, 'message' => 'Order item updated successfully'], 200);
    }
}
