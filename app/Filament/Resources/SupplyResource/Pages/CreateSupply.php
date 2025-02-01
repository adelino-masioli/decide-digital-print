<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Filament\Resources\SupplyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSupply extends CreateRecord
{
    protected static string $resource = SupplyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth()->user()->getTenantId();

        return $data;
    }
} 