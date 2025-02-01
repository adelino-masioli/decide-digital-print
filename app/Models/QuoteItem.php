<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'customization_options',
        'file_requirements',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'customization_options' => 'array',
        'file_requirements' => 'array',
    ];

    // Relacionamentos
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos auxiliares
    public function calculateTotal()
    {
        $this->total_price = $this->quantity * $this->unit_price;
        return $this->total_price;
    }
} 