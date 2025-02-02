<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth()->user()->getTenantId();
        
        if (auth()->user()->isTenantAdmin()) {
            $data['is_tenant_admin'] = false; // Apenas o primeiro admin pode ser tenant_admin
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
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