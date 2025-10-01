<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_price',
        'payment_status',
        'coupon_id',
        'subtotal',
        'discount',
        'currency',
        'invoice_id',
        'paid_at',
        'gateway_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'gateway_id');
    }

}
