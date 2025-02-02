<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTenant;

class Interaction extends Model
{
    use SoftDeletes, HasTenant;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'opportunity_id',
        'user_id',
        'type',
        'description',
        'scheduled_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
} 