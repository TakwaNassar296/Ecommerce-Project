<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory , SoftDeletes ;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'image',
        'category_id',
        'tenant_id' 
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getImageUrlAttribute()
    {
        if($this->image)
        {
            return asset('storage/'.$this->image);
        }
        
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

        static::creating(function ($product) {
            $admin = auth('admin')->user();

            if ($admin) {
                
                $product->tenant_id = $admin->tenant_id;

                $product->tenant()->associate($admin->tenant);
            }
        });
    }
}
