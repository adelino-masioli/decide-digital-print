<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function afterCreate(): void
    {
        $this->record->assignRole('client');
    }

    protected function onCreate(): void
    {
        try {
            $this->create();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                Notification::make()
                    ->danger()
                    ->title('Erro ao cadastrar')
                    ->body('Este documento já está cadastrado no sistema.')
                    ->send();
                
                $this->halt();
                return;
            }
            
            throw $e;
        }
    }
} 