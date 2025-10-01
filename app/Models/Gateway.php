<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gateway extends Model
{
    use HasFactory , SoftDeletes ; 

    protected $fillable = [
        'name' , 'is_active'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'gateway_id');
    }
}
