<?php

namespace Database\Seeders;

use App\Models\Cart;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Tenant;
use App\Models\Gateway;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Database\Factories\GatewayFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

          GatewaySeeder::class,
          
        ]);
        Tenant::factory(10)->create();
        User::factory(10)->create();
        Admin::factory(10)->create();
        Cart::factory(10)->create();
        Coupon::factory(10)->create();
        Order::factory(10)->create();
        Category::factory(10)->create();
        Product::factory(10)->create();
        ProductVariant::factory(10)->create();
    }
}
