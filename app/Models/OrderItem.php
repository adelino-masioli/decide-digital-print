<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'discount_amount',
        'customization_options',
        'file_requirements',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the description attribute with fallback.
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_bool($value) || is_null($value)) {
                    return '';
                }
                return $value;
            }
        );
    }

    /**
     * Get the customization options attribute.
     */
    protected function customizationOptions(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_bool($value) || is_null($value)) {
                    return null;
                }
                
                if (is_string($value) && !empty($value)) {
                    try {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            return $decoded;
                        }
                    } catch (\Exception $e) {
                        // Retorna o valor original se falhar
                    }
                }
                return $value;
            },
            set: function ($value) {
                if (is_array($value) && !empty($value)) {
                    return json_encode($value);
                }
                return $value;
            }
        );
    }

    /**
     * Get the file requirements attribute.
     */
    protected function fileRequirements(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_bool($value) || is_null($value)) {
                    return null;
                }
                
                if (is_string($value) && !empty($value)) {
                    try {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            return $decoded;
                        }
                    } catch (\Exception $e) {
                        // Retorna o valor original se falhar
                    }
                }
                return $value;
            },
            set: function ($value) {
                if (is_array($value) && !empty($value)) {
                    return json_encode($value);
                }
                return $value;
            }
        );
    }

    /**
     * Calculate the total price for this item.
     */
    public function getTotalPriceAttribute()
    {
        return ($this->quantity * $this->unit_price) - $this->discount_amount;
    }
} 