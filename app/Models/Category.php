<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'tenant_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Eventos do modelo
    protected static function boot()
    {
        parent::boot();

        // Gera o slug antes de salvar
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relacionamentos
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Escopo para categorias principais (sem pai)
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    // Escopo para categorias ativas
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Método para obter o caminho completo da categoria
    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $category = $this;

        while ($category->parent) {
            $category = $category->parent;
            array_unshift($path, $category->name);
        }

        return implode(' > ', $path);
    }
} 