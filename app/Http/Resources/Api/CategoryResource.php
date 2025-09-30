<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'slug' => $this->slug,
            'parent' => $this->parent->name ?? null,
            'products' => $this->products->map(function($product){
               return[
                'name' => $product->name,
                'description' => $product->description,
                'slug' => $product->slug,
                'image' => $product->ImageUrl,
               ];
            })
        ];
    }
}
