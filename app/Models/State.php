<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uf',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
} 