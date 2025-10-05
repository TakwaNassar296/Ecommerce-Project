<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory , SoftDeletes ;

    protected $fillable = [
        'name' , 'domain' , 'logo' , 'email' , 'phone' , 'address' , 'currency' , 'description'
    ];
    
    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class , 'tenant_user');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function gateways()
    {
        return $this->hasMany(Gateway::class);
    }

    public function personalAccessTokens()
    {
        return $this->hasMany(PersonalAccessToken::class);
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }
}