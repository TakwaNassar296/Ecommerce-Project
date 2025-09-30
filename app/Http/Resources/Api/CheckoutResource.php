<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'orderId'    => $this->id,
            'userId'     => $this->user_id,
            'subtotal'    => $this->subtotal,
            'totalPrice' => $this->total_price,
            'coupon'      => $this->coupon ? [
                'code'          => $this->coupon->code,
                'discount'      => $this->coupon->discount,
                'discountType' => $this->coupon->discount_type,
            ] : null,
            'items'       => $this->items->map(function ($item) {
                return [
                    'productVariantId' => $item->product_variant_id,
                    'name'               => $item->name,
                    'quantity'           => $item->quantity,
                    'price'              => $item->price,
                    'total'              => $item->total,
                ];
            }),
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}
