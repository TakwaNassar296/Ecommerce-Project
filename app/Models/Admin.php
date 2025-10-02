<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Support\Collection;


class Admin extends Authenticatable implements FilamentUser , HasTenants
{
    use HasFactory , SoftDeletes ;

    protected $fillable = [
        'name' ,
        'email' ,
        'password' ,
    ];

    protected $hidden = [
        'password' ,
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->tenant ? collect([$this->tenant]) : collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->tenant()->whereKey($tenant)->exists();
    }
}
