<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'couponId' => $this->coupon_id,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'total' => $this->items->sum(fn($item) => $item->price * $item->quantity),
        ];
    }
}
