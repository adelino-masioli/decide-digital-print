<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sku',
        'category_id',
        'tenant_id',
        'keywords',
        'format',
        'material',
        'weight',
        'finishing',
        'color',
        'production_time',
        'min_quantity',
        'max_quantity',
        'customization_options',
        'file_requirements',
        'base_price',
        'is_active',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'customization_options' => 'array',
        'file_requirements' => 'array',
        'is_active' => 'boolean',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'production_time' => 'integer',
        'base_price' => 'decimal:2',
    ];

    // Eventos do modelo
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = strtoupper(Str::random(8));
            }
        });
    }

    // Relacionamentos
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function supplies()
    {
        return $this->belongsToMany(Supply::class)
            ->withPivot(['quantity', 'unit'])
            ->withTimestamps();
    }

    // Escopos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 