<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'is_active',
        'tenant_id' 
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

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function (Builder $query) {

            $admin = auth('admin')->user();
            if ($admin) {

                $query->where('tenant_id', $admin->tenant_id);

                $query->whereBelongsTo($admin->tenant , 'tenant');
            }
        });


        static::creating(function ($coupon) {
            $admin = auth('admin')->user();

            if ($admin) {
                
                $coupon->tenant_id = $admin->tenant_id;

                $coupon->tenant()->associate($admin->tenant);
            }
        });
    }

    

}
