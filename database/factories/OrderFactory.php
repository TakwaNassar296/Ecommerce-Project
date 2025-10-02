<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Gateway;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(100, 1000);
        $discount = $this->faker->randomElement([0, 10, 20, 50]);
        $total    = $subtotal - $discount;

        return [
            'tenant_id'      => Tenant::factory(), 
            'user_id'        => User::factory(),
            'coupon_id'      => $this->faker->optional()->randomElement([Coupon::factory()]),
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'total_price'    => $total,
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'currency'       => 'EGP',
            'invoice_id'     => strtoupper($this->faker->bothify('INV###')),
            'paid_at'        => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'gateway_id'     => Gateway::where('name' , 'myfatoorah')->first()->id,
        ];
    }
}
