<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id' , $request->user()->id)->paginate(10);
        
        if($orders->isEmpty())
        {
            return $this->sendResponse('No orders found' , null , 404);
        }

        $data=[
            'total' => $orders->total(),
            'per_page' =>  $orders->perPage(),
            'current_page' =>  $orders->currentPage(),
            'last_page' =>  $orders->lastPage(),
            'orders' =>OrderResource::collection($orders),
        ];

        return $this->sendResponse('orders retrieved successfully' , $data , 200);
    }

    public function show(Request $request , $id)
    {
        $order = Order::where('user_id' , $request->user()->id)
        ->find($id);
        
        if(!$order)
        {
            return $this->sendResponse('Order not found' , null , 404);
        }

        return $this->sendResponse('order retrieved successfully' , new OrderResource($order) , 200);

    }


    public function cancel(Request $request , $id)
    {
        $order = Order::where('user_id' , $request->user()->id)
        ->where('payment_status' , 'pending')
        ->find($id);

        if(!$order)
        {
            return $this->sendResponse('Order not found' , null , 404);
        }

        $order->payment_status = 'canceled';
        $order->save();
        
       return $this->sendResponse('Order canceled successfully' , null , 200); 
    }
}
