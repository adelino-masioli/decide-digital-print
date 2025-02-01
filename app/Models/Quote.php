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
        'valid_until',
        'status',
        'client_id',
        'seller_id',
        'tenant_id',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    // Definir as constantes para os status
    public const STATUS_DRAFT = 'draft';
    public const STATUS_OPEN = 'open';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CONVERTED = 'converted';
    public const STATUS_CANCELED = 'canceled';

    // Definir os status possíveis
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_OPEN,
            self::STATUS_APPROVED,
            self::STATUS_EXPIRED,
            self::STATUS_CONVERTED,
            self::STATUS_CANCELED,
        ];
    }

    // Boot do modelo
    protected static function boot()
    {
        parent::boot();

        // Gerar número do orçamento antes de criar
        static::creating(function ($quote) {
            if (!$quote->number) {
                $quote->number = 'ORC-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relacionamentos
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
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
        return $this->status === self::STATUS_OPEN && !$this->isExpired();
    }

    public function canBeConverted(): bool
    {
        return $this->status === self::STATUS_APPROVED && !$this->order()->exists();
    }
} 