<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'tenant_id'   => Tenant::factory(), 
            'code'        => strtoupper($this->faker->bothify('CODE###')),
            'type'        => $this->faker->randomElement(['fixed' , 'percentage']),
            'value'       => $this->faker->numberBetween(5, 50),
            'expires_at'  => $this->faker->optional()->dateTimeBetween('now', '+3 months'),
            'usage_limit' => $this->faker->numberBetween(10, 100),
            'used_count'  => 0,
            'is_active'   => true,
        ];
       
    }
}
