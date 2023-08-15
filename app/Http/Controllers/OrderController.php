<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{

    // creating orders

    public function createOrder(Request $request){
        $date = Carbon::now();
        $user = JWTAuth::parseToken()->authenticate();
        $currentDateAndTime = $date->format('Y-m-d H:i:s');
        if($user)
        {
            $currentUser = $user->id;
        }else{
            return response()->json(['success'=>false , 'message'=>'no user found'],404);
        }
        $order=Order::create([
            'u_id'=>$currentUser,
            'order_date'=>$currentDateAndTime,
        ]);
        return response()->json(['success'=>true , 'message'=>'successfully created the order' , 'order'=>$order],200);
    }

    // getting all orders

    public function showAllOrders(){
        $ordersWithUserDetails = Order::join('users', 'orders.u_id', '=', 'users.id')
        ->select('users.username', 'users.email', 'orders.*')
        ->get();

        if($ordersWithUserDetails){
            return response()->json(['success'=>true , 'userorderdetail'=>$ordersWithUserDetails],200);
        }else{
            return response()->json(['success'=>false , 'message' => 'error in fetching details'],404);
        }
    }

    // updating order status

    public function updateOrderStatus(Request $request , $orderId){
        $statusValidation = Validator::make($request->all(), [
            'status' => 'required|in:shipped,unshipped',
        ]);
        if($statusValidation->fails()){
            return response()->json(['success'=>false , 'message'=>'field is required'],400);
        }
        $order = Order::find($orderId);
        if(!$order){
            return response()->json(['success'=>false , 'message' => 'order not found'],404);
        }
        $validatedData = $statusValidation->validated();
        $order->status = $validatedData['status'];
        $order->save();
        return response()->json(['success' => true, 'message' => 'Order status updated successfully'],200);
    }

    // filter the unshipped order

    public function getUnshippedOrderDetails($order_id)
    {
        $unshippedOrder = Order::where('id', $order_id)
            ->where('status', 'unshipped')
            ->with('orderItems.book')
            ->first();

        if (!$unshippedOrder) {
            return response()->json(['success' => false, 'message' => 'Unshipped order not found'], 404);
        }
        return response()->json(['success' => true, 'order' => $unshippedOrder],200);
    }
}