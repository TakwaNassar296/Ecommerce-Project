<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory , SoftDeletes ;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get($key , $default = null)
    {
       return static::where('key' , $key)->value('value') ?? $default ;
    }

    public static function set($key , $value)
    {
       return static::updateOrCreate(['key' , $key] , ['value' , $value]) ;
    }
}
