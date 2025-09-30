<?php

namespace App\Payments;

use Illuminate\Http\Request;

interface PaymentInterface
{
    public function pay(array $data);
    //public function handleCallback(Request $request);
}