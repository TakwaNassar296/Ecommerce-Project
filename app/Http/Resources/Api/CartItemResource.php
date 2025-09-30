<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'productVariantName' => $this->variant->sku,
            'quantity' => $this->quantity ,
            'price' => $this->price ,
            'total' => $this->price * $this->quantity,
            'cartId' => $this->cart_id,
        ];
    }
}
