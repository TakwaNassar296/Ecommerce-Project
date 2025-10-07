<?php

namespace App\Jobs;

use App\Mail\OrderCreatedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Queueable , Dispatchable , InteractsWithQueue , SerializesModels;


    public $order ;

    /**
     * Create a new job instance.
     */
    public function __construct($order)
    {
        $this->order = $order ;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = $this->order->user->email ;
        
        if($email)
        {
            Mail::to($email)->send(new OrderCreatedMail($this->order));
        }
    }
}
