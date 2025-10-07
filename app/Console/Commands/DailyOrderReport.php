<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use App\Mail\DailyOrdersReportMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DailyOrderReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-order-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily report of yesterday’s orders count to admin email.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterday = now()->subDay()->toDateString();

        $count = Order::whereDate('created_at', $yesterday)->count();

        Log::info("📊 Daily Report: {$count} orders created on {$yesterday}");

        if ($count > 0) {
            Mail::to('admin@example.com')->send(new DailyOrdersReportMail($count, $yesterday));
        }

        $this->info("Daily report generated successfully ✅ ({$count} orders)");
    }
}