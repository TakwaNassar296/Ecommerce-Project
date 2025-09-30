<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_when_cart_is_empty()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/cart/checkout');

        $response->assertStatus(400)
                 ->assertJson(['error' => 'Cart is empty']);
    }

   #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_order_without_coupon()
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $variant = ProductVariant::factory()->create();

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'price' => $variant->price,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->postJson('/api/cart/checkout');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'total_price',
                         'sub_total',
                         'items'
                     ]
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_applies_valid_coupon()
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $variant = ProductVariant::factory()->create();

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'price' => $variant->price,
            'quantity' => 1,
        ]);

        $coupon = Coupon::factory()->create([
            'code' => 'DISCOUNT10',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)->postJson('/api/cart/checkout', [
            'coupon_code' => 'DISCOUNT10',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.coupon_id', $coupon->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_with_expired_coupon()
    {
        $user = User::factory()->create();

        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $variant = ProductVariant::factory()->create();

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'price' => $variant->price,
            'quantity' => 1,
        ]);

        Coupon::factory()->create([
            'code' => 'OLD',
            'type' => 'percentage',
            'value' => 20,
            'is_active' => true,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->postJson('/api/cart/checkout', [
            'coupon_code' => 'OLD',
        ]);

        $response->assertStatus(400);
    }
                 
}