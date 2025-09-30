<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'price' ,
        'total'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }


    public function getTotalAttribute()
    {
        return $this->price * $this->quantity ;
    }
}
