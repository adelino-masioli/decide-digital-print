<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTenant;

class Order extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'number',
        'quote_id',
        'tenant_id',
        'client_id',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Definir as constantes para os status
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PROCESSING = 'processing';
    const STATUS_IN_PRODUCTION = 'in_production';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELED = 'canceled';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    // Definir os status possíveis
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_PROCESSING,
            self::STATUS_IN_PRODUCTION,
            self::STATUS_COMPLETED,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELED,
        ];
    }

    public static function getPaymentStatuses(): array
    {
        return [
            self::PAYMENT_STATUS_PENDING,
            self::PAYMENT_STATUS_PAID,
            self::PAYMENT_STATUS_FAILED,
            self::PAYMENT_STATUS_REFUNDED,
        ];
    }

    // Relacionamentos
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function items()
    {
        return $this->hasOneThrough(
            QuoteItem::class,
            Quote::class,
            'id', // Chave em quotes
            'quote_id', // Chave em quote_items
            'quote_id', // Chave em orders
            'id' // Chave em quotes
        );
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Métodos auxiliares
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function canBeProduced(): bool
    {
        return $this->isPaid() && $this->status === 'processing';
    }

    public function markAsPaid()
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'status' => 'processing'
        ]);
    }
} 