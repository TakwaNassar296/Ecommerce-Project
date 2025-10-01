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
    public function pay(Request $request)
    {
        
        $order = Order::with('items.variant.product')
                    ->where('user_id', $request->user()->id)
                    ->firstOrFail();

        $orderItems = $order->items;

        $total = $orderItems->sum('total');

        $items = $orderItems->map(function ($item) {
            return [
                'ItemName'  => $item->variant->product->name ?? 'Default Item',
                'Quantity'  => (int) $item->quantity,
                'UnitPrice' => (float) $item->price,
            ];
        })->values()->toArray();

        $data = [
            'amount'          => $total,
            'customer_name'   => $request->user()->name ?? 'Guest',
            'customer_email'  => $request->user()->email ?? 'guest@example.com',
            'Customer_mobile' => $request->user()->phone ?? '01210569957',
            'items'           => $items,
            'callback_url'    => route('payment.callback'),
            'error_url'       => route('payment.error'),
            'currency'        => 'USD',
        ];

        $gateway = PaymentFactory::make('myfatoorah');

        $paymentResponse = $gateway->pay($data);

        return response()->json($paymentResponse);
    }
    public function paymentCallback(Request $request)
    {
        $invoiceId = $request->input('invoiceId'); 
        $gateway = PaymentFactory::make('myfatoorah');
        $result = $gateway->handleCallback($invoiceId); 
        return response()->json($result);
    }
}