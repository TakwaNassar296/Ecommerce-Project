<?php

namespace App\Payments ;
use App\Models\Order;
use MyFatoorah\Library\MyFatoorah;
use Illuminate\Http\Request;

class MyfatoorahService implements PaymentInterface
{
    protected $payment;

    public function __construct()
    {
        $config = [
            'apiKey' => env('MYFATOORAH_API_KEY'),         
            'isTest' => env('MYFATOORAH_SANDBOX', true), 
            'vcCode' => 'EGY' , 
        ];

        $this->payment = new MyFatoorah($config);
    }

    public function pay(array $data)
    {
        $invoiceData = [
            'PaymentMethodId' => 'MyFatoorah',
            'InvoiceValue' => $data['amount'],
            'CustomerName' => $data['customer_name'],
            'CustomerEmail' => $data['customer_email'],
            'CustomerMobile' => $data['Customer_mobile'],
            'DisplayCurrencyIso' => $data['currency'] ?? 'EGP',
            'InvoiceItems' => $data['items'],
            'CallBackUrl' => $data['callback_url'],
            'ErrorUrl' => $data['error_url'],
            'NotificationOption' => 'All'
        ];

        $url = 'https://apitest.myfatoorah.com/v2/SendPayment' ; 


        $response = $this->payment->callAPI($url , $invoiceData);
        
        return [
            'message' => $response->Message ?? 'Payment request sent',
            'invoiceId' => $response->Data->InvoiceId,
            'InvoiceUrl' => $response->Data->InvoiceURL,
        ];
    }
    /*public function handleCallback(Request $request)
    {
        $invoiceId = $request->input('invoiceId');

        // استعلام MyFatoorah رسميًا عن حالة الدفع
        $response = $this->payment->getAPIError($invoiceId); // استخدمي الميثود المناسبة من المكتبة

        $status = $response->InvoiceStatus ?? 'Failed';

        $order = Order::where('invoice_id', $invoiceId)->first();
        if (!$order) {
            return ['success' => false, 'message' => 'Order not found'];
        }

        if ($status === 'Paid') {
            $order->status = 'paid';
            $order->paid_at = now();
            $order->save();
            return ['success' => true, 'message' => 'Payment successful', 'order_id' => $order->id];
        } else {
            $order->status = 'failed';
            $order->save();
            return ['success' => false, 'message' => 'Payment failed', 'order_id' => $order->id];
        }
    }*/
}