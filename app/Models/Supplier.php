<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTenant;

class Supplier extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'name',
        'contact_info',
        'email',
        'phone',
        'postal_code',
        'address',
        'neighborhood',
        'state_id',
        'city_id',
        'tenant_id',
    ];

    // Relacionamentos
    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
} 