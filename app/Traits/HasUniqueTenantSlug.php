<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasUniqueTenantSlug
{
    /**
     * Gera um slug único para o modelo dentro do escopo do tenant
     */
    public function generateUniqueSlug(Model $model, string $name): string
    {
        $slug = Str::slug($name);
        
        // Verifica se já existe um registro com este slug para o mesmo tenant
        $count = $model->where('tenant_id', $model->tenant_id)
            ->where('slug', 'like', $slug . '%')
            ->when($model->exists, fn($query) => $query->where('id', '!=', $model->id))
            ->count();

        // Se já existir, adiciona um número incremental ao final
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        return $slug;
    }

    /**
     * Boot do trait
     */
    public static function bootHasUniqueTenantSlug()
    {
        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = $model->generateUniqueSlug($model, $model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name')) {
                $model->slug = $model->generateUniqueSlug($model, $model->name);
            }
        });
    }
} 