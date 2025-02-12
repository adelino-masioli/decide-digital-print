<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasTenant;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'document',
        'phone',
        'password',
        'tenant_id',
        'is_active',
        'is_tenant_admin',
        'company_name',
        'trading_name',
        'state_registration',
        'municipal_registration',
        'company_address',
        'company_logo',
        'company_latitude',
        'company_longitude',
        'seo_text',
        'welcome_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_tenant_admin' => 'boolean',
        'welcome_confirmed_at' => 'datetime',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Métodos auxiliares
    public function isTenantAdmin(): bool
    {
        return $this->is_tenant_admin;
    }

    public function getTenantId()
    {
        return $this->tenant_id;
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isOperator(): bool
    {
        return $this->hasRole('operator');
    }

    public function isClient(): bool
    {
        return $this->hasRole('client');
    }

    public function tenantUsers()
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function updateAddress(array $data)
    {
        return $this->address()->updateOrCreate(
            ['user_id' => $this->id],
            $data
        );
    }

    public function getStateAttribute()
    {
        return $this->address?->state;
    }

    public function getCityAttribute()
    {
        return $this->address?->city;
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class, 'user_id');
    }

    public function managedOpportunities()
    {
        return $this->hasMany(Opportunity::class, 'responsible_id');
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }
}
