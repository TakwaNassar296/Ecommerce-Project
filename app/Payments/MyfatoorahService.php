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
    public function handleCallback($invoiceId)
    {
        $order = Order::where('invoice_id', $invoiceId)->firstOrFail();

        $url = ($this->payment->config['isTest'] ?? true)
            ? 'https://apitest.myfatoorah.com/v2/GetPaymentStatus'
            : 'https://api.myfatoorah.com/v2/GetPaymentStatus';

        $response = $this->payment->callAPI($url, [
            'Key' => $invoiceId,
            'KeyType' => 'InvoiceId'
        ]);

        $status = $response->Data->InvoiceStatus ?? 'Failed';

        if ($status === 'Paid') {
            $order->status = 'paid';
            $order->paid_at = now();
            $order->save();

            return [
                'success' => true,
                'message' => 'Payment successful',
                'order_id' => $order->id
            ];
        } else {
            $order->status = 'failed';
            $order->save();

            return [
                'success' => false,
                'message' => 'Payment failed',
                'order_id' => $order->id
            ];
        }
    }
}