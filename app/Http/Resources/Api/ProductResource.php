<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'image' => $this->ImageUrl,
            'category' => $this->category->name,
            'variants' => $this->variants->map(function($variant){
               return[
                'sku' => $variant->sku,
                'price'  => $variant->price,
                'stock' => $variant->stock,
                'attributes' => $variant->attributes ?? [],
                'images' => $variant->ImageUrls,
               ];
            }),
        ];
    }
}
