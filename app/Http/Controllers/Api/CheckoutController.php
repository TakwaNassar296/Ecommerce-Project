<?php
namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Payments\PaymentFactory;
use App\Payments\MyfatoorahService;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function pay(Request $request, MyfatoorahService $gateway)
    {

        $order = Order::where('user_id' , $request->user()->id)->first();

        $orderItems = OrderItem::where('order_id', $order->id)->get(); 

        $total = $orderItems->sum('total'); 

        $items = $orderItems->map(function($item) {
            return [
                'ItemName'  => $item->variant->name, 
                'Quantity'  => $item->quantity,
                'UnitPrice' => $item->price,
            ];
        })->toArray();

        $data = [
            'amount' => $total,
            'customer_name' => $request->user()->name ?? 'Guest',
            'customer_email' => $request->user()->email ?? 'guest@example.com',
            'Customer_mobile' => $request->user()->phone ?? '01210569957',
            'items' => $items,
            'callback_url' => route('payment.callback'),
            'error_url' => route('payment.error'),
            'currency' => 'USD',
        ];


        $paymentUrl = $gateway->pay($data);

        return response()->json($paymentUrl);

    }

    /*public function paymentCallback(Request $request)
    {
        $paymentMethod = $request->input('payment_method'); 

        $gateway = PaymentFactory::make($paymentMethod);

        $result = $gateway->handleCallback($request);

        
        if ($result['success']) {
            return redirect()->route('checkout.success')->with('success', $result['message']);
        } else {
            return redirect()->route('checkout.failed')->with('error', $result['message']);
        }
    }*/
}