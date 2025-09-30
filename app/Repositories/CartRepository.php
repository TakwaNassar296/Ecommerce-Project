<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class CartRepository
{
    
    public function getUserCart($userId)
    {
        return Cart::with('items.variant')->where('user_id', $userId)->first();
    }

    public function addItem($userId, $variantId, $quantity)
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $variant = ProductVariant::findOrFail($variantId);

        $item = $cart->items()->where('product_variant_id', $variantId)->first();

        if ($item) {

            $item->quantity += $quantity;
            $item->price = $variant->price;
            $item->save();
        } else {
            $item = $cart->items()->create([
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $variant->price,
            ]);
        }

        return $item;
    }

    public function updateItem($cartItemId, $addedQuantity)
    {
        $item = CartItem::findOrFail($cartItemId);

        if(!$item)
        {
           throw new \Exception('No cart item found'); 
        }

        $item->quantity += $addedQuantity;
        $item->price = $item->variant->price ;
        $item->save();
        return $item;
    }

    public function removeItem($cartItemId)
    {
        return CartItem::findOrFail($cartItemId)->delete();
    }

    public function clearCart($userId)
    {
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            throw new \Exception('Cart not found');
        }

        $cart->items()->delete();
         $cart->delete();

        return true;
    }

   
    public function checkout($userId, $couponCode = null)
    {
        $cart = $this->getUserCart($userId);

        if (!$cart || $cart->items->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        return DB::transaction(function () use ($cart, $couponCode) {
            $coupon = null;

            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)
                    ->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                    })
                    ->first();

                if (!$coupon) {
                    throw new \Exception('Coupon is invalid or expired');
                }

                if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                    throw new \Exception('Coupon usage limit reached');
                }

                $alreadyUsed = Order::where('user_id', $cart->user_id)
                    ->where('coupon_id', $coupon->id)
                    ->exists();

                if ($alreadyUsed) {
                    throw new \Exception('You have already used this coupon');
                }
            }

            $subtotal = 0;

          
            foreach ($cart->items as $item) {
                $subtotal += $item->price * $item->quantity;
            }

            $total = $subtotal;

            if ($coupon) {
                if ($coupon->discount_type === 'percentage') {
                    $total -= ($total * ($coupon->value / 100));
                } else {
                    $total -= $coupon->value;
                }

                if ($total < 0) {
                    $total = 0;
                }

                $coupon->increment('used_count');
            }

            $order = Order::create([
                'user_id'     => $cart->user_id,
                'coupon_id'   => $coupon ? $coupon->id : null,
                'subtotal'    => $subtotal,
                'total_price' => $total,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity'           => $item->quantity,
                    'name'               => $item->variant->product->name,
                    'price'              => $item->price,
                    'total'              => $item->price * $item->quantity,
                ]);
            }
            $cart->items()->delete();
            $cart->delete();

            return $order;
        });
    }
}