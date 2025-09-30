<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory , SoftDeletes ;

    protected $fillable = [
        'code',
        'type',
        'value',
        'expires_at',
        'usage_limit',
        'used_count',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime' ,
    ];


    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isvalid(): bool 
    {
        return 
            $this->is_active &&
            ($this->usage_limit === null || $this->used_count < $this->usage_limit ) &&
            ($this->expires_at === null || $this->expires_at->isFuture() ) ;

    }

}
