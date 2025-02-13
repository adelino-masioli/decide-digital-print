<?php

namespace App\Models;

use App\Traits\HasTenant;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasTenant;

    protected $fillable = [
        'name',
        'guard_name',
        'tenant_id'
    ];

    public static function boot()
    {
        parent::boot();

        // Adiciona o tenant_id ao criar um novo role
        static::creating(function ($model) {
            if (auth()->check()) {
                $user = auth()->user();
                $model->tenant_id = $user->is_tenant_admin ? $user->id : $user->tenant_id;
            }
        });
    }

    // Scope para filtrar por tenant
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // Relacionamento com o tenant
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
} 