<?php

namespace App\Models;

use Filament\Actions\Exports\Models\Export as BaseExport;

class Export extends BaseExport
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (self $export) {
            $export->file_name = $export->file_name ?? 'clientes-' . now()->format('Y-m-d');
            $export->file_disk = $export->file_disk ?? 'local';
        });
    }
} 