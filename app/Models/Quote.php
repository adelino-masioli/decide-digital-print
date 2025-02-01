<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTenant;

class Quote extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'number',
        'tenant_id',
        'client_id',
        'seller_id',
        'status',
        'valid_until',
        'total_amount',
        'notes',
        'version_history',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'total_amount' => 'decimal:2',
        'version_history' => 'array',
    ];

    // Relacionamentos
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    // Métodos auxiliares
    public function isExpired(): bool
    {
        return $this->valid_until->isPast();
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, ['draft', 'open']) && !$this->isExpired();
    }

    public function canBeConverted(): bool
    {
        return $this->status === 'approved' && !$this->order()->exists();
    }
} 