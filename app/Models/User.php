<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'otp',
        'otp_expires'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class , 'tenant_user');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function (Builder $query) {
            $admin = auth('admin')->user();

            if ($admin && $admin->tenant_id) {
                $query->whereHas('tenants', function ($q) use ($admin) {
                    $q->where('tenants.id', $admin->tenant_id);
                });
            }
        });

        static::created(function ($user) {
            $admin = auth('admin')->user();

            if ($admin && $admin->tenant_id) {
                $user->tenants()->syncWithoutDetaching($admin->tenant_id);
            }
        });
    }
   
}
