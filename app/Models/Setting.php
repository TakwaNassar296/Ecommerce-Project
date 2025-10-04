<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory , SoftDeletes ;

    protected $fillable = [
        'key',
        'value',
        'tenant_id' 
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function get($key , $default = null)
    {
       return static::where('key' , $key)->value('value') ?? $default ;
    }

    public static function set($key , $value)
    {
       return static::updateOrCreate(['key' , $key] , ['value' , $value]) ;
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


        static::creating(function ($setting) {
            $admin = auth('admin')->user();

            if ($admin) {
                
                $setting->tenant_id = $admin->tenant_id;

                $setting->tenant()->associate($admin->tenant);
            }
        });
    }
}
