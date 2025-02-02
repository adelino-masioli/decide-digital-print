<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasTenant
{
    protected static function bootHasTenant()
    {
        static::creating(function ($model) {
            if (!$model->tenant_id && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        // Aplicar o escopo global apenas se estiver autenticado
        if (auth()->check()) {
            static::addGlobalScope('tenant', function (Builder $query) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            });
        }
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
} 