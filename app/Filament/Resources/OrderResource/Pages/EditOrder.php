<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\NotificationManager;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markAsPaid')
                ->label('Marcar como Pago')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->payment_status === 'pending')
                ->action(function () {
                    $this->record->markAsPaid();
                    
                    Notification::make()
                        ->success()
                        ->title('Pedido marcado como pago com sucesso!')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
                }),

            Actions\Action::make('startProduction')
                ->label('Iniciar Produção')
                ->icon('heroicon-o-play')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->canBeProduced())
                ->action(function () {
                    $this->record->update(['status' => 'in_production']);

                    Notification::make()
                        ->success()
                        ->title('Pedido enviado para produção!')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
                }),

            Actions\Action::make('completeOrder')
                ->label('Concluir Pedido')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->status === 'in_production')
                ->action(function () {
                    $this->record->update(['status' => 'completed']);
                    Notification::make()
                        ->success()
                        ->title('Pedido concluído com sucesso!')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
                }),

            Actions\DeleteAction::make(),
        ];
    }
} 