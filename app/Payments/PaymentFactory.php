<?php

namespace App\Payments;

use App\Payments\PaymentInterface;
use Exception;

class PaymentFactory
{
    public static function make(string $gateway):PaymentInterface
    {
        return match($gateway){
            'myfatoorah' => new MyfatoorahService(),
            default => throw new Exception('Unsupported payment gateway'),
        };

    }
}