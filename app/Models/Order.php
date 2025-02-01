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

    // Status do Pedido
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_IN_PRODUCTION = 'in_production';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';

    // Métodos de Pagamento
    public const PAYMENT_METHOD_CASH = 'cash';
    public const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';
    public const PAYMENT_METHOD_PIX = 'pix';
    public const PAYMENT_METHOD_BANK_SLIP = 'bank_slip';

    // Status do Pagamento
    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED = 'failed';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_PROCESSING,
            self::STATUS_IN_PRODUCTION,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELED,
        ];
    }

    public static function getPaymentMethods(): array
    {
        return [
            self::PAYMENT_METHOD_CASH,
            self::PAYMENT_METHOD_CREDIT_CARD,
            self::PAYMENT_METHOD_PIX,
            self::PAYMENT_METHOD_BANK_SLIP,
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
            'payment_status' => self::PAYMENT_STATUS_PAID,
            'status' => self::STATUS_PROCESSING,
            'paid_at' => now(),
        ]);
    }
} 