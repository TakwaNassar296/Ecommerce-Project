<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RefreshToken extends Model
{
    use HasFactory , SoftDeletes ;

    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'revoked',
        'ip',
        'user_agent'
    ];


    protected $casts = [
        'expires_at' => 'datetime' ,
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired() :bool
    {
       return $this->expires_at->isPast();
    }


    public function isValid() :bool
    {
       return  !$this->revoked &&!$this->isExpired();
    }

}
