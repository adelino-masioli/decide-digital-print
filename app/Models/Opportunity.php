<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTenant;

class Opportunity extends Model
{
    use SoftDeletes, HasTenant;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'responsible_id',
        'title',
        'description',
        'value',
        'status',
        'expected_closure_date',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'expected_closure_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    public function items()
    {
        return $this->hasMany(OpportunityItem::class);
    }

    public function calculateTotal()
    {
        // Força uma nova consulta para pegar os valores mais recentes
        return $this->items()->sum('total_price') ?? 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($opportunity) {
            // Calcula o valor total baseado nos itens
            if (!$opportunity->value && $opportunity->items) {
                $opportunity->value = $opportunity->items->sum('total_price');
            }
        });

        static::saving(function ($opportunity) {
            $opportunity->value = $opportunity->calculateTotal();
        });
    }
} 