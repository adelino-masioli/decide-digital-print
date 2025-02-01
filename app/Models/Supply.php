<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTenant;

class Supply extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'name',
        'description',
        'supplier_id',
        'unit',
        'stock',
        'min_stock',
        'cost_price',
        'tenant_id',
    ];

    protected $casts = [
        'stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    // Relacionamentos
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['quantity', 'unit'])
            ->withTimestamps();
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    // Métodos auxiliares
    public function needsRestock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
} 