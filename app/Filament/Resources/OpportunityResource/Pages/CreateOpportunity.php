<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOpportunity extends CreateRecord
{
    protected static string $resource = OpportunityResource::class;

    // Traduzindo o título
    protected static ?string $title = 'Criar Oportunidade';



    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 