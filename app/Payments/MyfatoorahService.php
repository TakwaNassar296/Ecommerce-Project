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
            'DisplayCurrencyIso' => $data['currency'] ,
            'InvoiceItems' => $data['items'],
            'CallBackUrl' => $data['callback_url'],
            'ErrorUrl' => $data['error_url'],
            'NotificationOption' => 'All'
        ];

        $url = ($this->payment->config['isTest'] ?? true) 
            ? 'https://apitest.myfatoorah.com/v2/SendPayment'
            : 'https://api.myfatoorah.com/v2/SendPayment';


        $response = $this->payment->callAPI($url , $invoiceData);
        
        return [
            'message' => $response->Message ?? 'Payment request sent',
            'invoiceId' => $response->Data->InvoiceId,
            'InvoiceUrl' => $response->Data->InvoiceURL,
        ];
    }
    public function handleCallback($PaymentId)
    {
        $url = ($this->payment->config['isTest'] ?? true)
            ? 'https://apitest.myfatoorah.com/v2/GetPaymentStatus'
            : 'https://api.myfatoorah.com/v2/GetPaymentStatus';

        $response = $this->payment->callAPI($url, [
            'Key' => $PaymentId,
            'KeyType' => 'PaymentId'
        ]);


        $InvoiceId = $response->Data->InvoiceId ?? null;

        $status = $response->Data->InvoiceStatus ?? 'unknown';

        if(!$InvoiceId)
        {
            return [
                'success' => false , 
                'message' => 'No InvoiceId returned from my fatoorah'
            ];
        }

        $order = Order::where('invoice_id' , $InvoiceId)->first();

        if(!$order){
            return [
                'success' => false , 
                'message' => 'Order not found for invoice' . $InvoiceId ,
            ];
        }

       switch ($status) {
        case 'Paid':
            $order->payment_status = 'paid';
            $order->paid_at = now();
            $order->save();
            return [
                'success' => true,
                'message' => 'Payment successful',
                'order_id' => $order->id
            ];

        case 'Pending':
            $order->payment_status = 'pending';
            $order->save();
            return [
                'success' => false,
                'message' => 'Payment still pending',
                'order_id' => $order->id
            ];

        case 'Expired':
        case 'Canceled':
            $order->payment_status = strtolower($status);
            $order->save();
            return [
                'success' => false,
                'message' => "Payment $status",
                'order_id' => $order->id
            ];

        default:
            $order->payment_status = 'unknown';
            $order->save();
            return [
                'success' => false,
                'message' => 'Unknown payment status',
                'order_id' => $order->id
            ];
    }    }
}