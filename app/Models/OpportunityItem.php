<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpportunityItem extends Model
{
    protected $fillable = [
        'opportunity_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'customization_options',
        'file_requirements',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'customization_options' => 'array',
        'file_requirements' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Acessor para garantir que o total sempre esteja correto
    public function getTotalPriceAttribute($value)
    {
        return number_format($this->quantity * $this->unit_price, 2, '.', '');
    }

    // Mutator para formatar o preço unitário
    public function setUnitPriceAttribute($value)
    {
        if (is_string($value)) {
            $value = (float) str_replace(['.', ','], ['', '.'], $value);
        }
        $this->attributes['unit_price'] = $value;
    }

    // Mutator para formatar o preço total
    public function setTotalPriceAttribute($value)
    {
        if (is_string($value)) {
            $value = (float) str_replace(['.', ','], ['', '.'], $value);
        }
        $this->attributes['total_price'] = $value;
    }
} 