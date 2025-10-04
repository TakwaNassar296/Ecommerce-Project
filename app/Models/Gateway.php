<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gateway extends Model
{
    use HasFactory , SoftDeletes ; 

    protected $fillable = [
        'name' , 'is_active' , 'tenant_id' , 'api_key' , 'is_test'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'gateway_id');
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


        static::creating(function ($gateway) {
            $admin = auth('admin')->user();

            if ($admin) {
                
                $gateway->tenant_id = $admin->tenant_id;

                $gateway->tenant()->associate($admin->tenant);
            }
        });
    }
}
