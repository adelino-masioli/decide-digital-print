<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::creating(function (Model $model) {
            if (!$model->tenant_id && auth()->check()) {
                $model->tenant_id = auth()->user()->getTenantId();
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('tenant_id', auth()->user()->getTenantId());
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
} 