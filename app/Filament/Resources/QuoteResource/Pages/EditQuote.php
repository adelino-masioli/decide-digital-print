<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Order;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Aprovar')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->canBeApproved())
                ->action(function () {
                    $this->record->update(['status' => 'approved']);
                    $this->notify('success', 'Orçamento aprovado com sucesso!');
                }),

            Actions\Action::make('convert')
                ->label('Converter em Pedido')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->canBeConverted())
                ->action(function () {
                    // Cria o pedido
                    Order::create([
                        'number' => 'PED-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT),
                        'quote_id' => $this->record->id,
                        'tenant_id' => $this->record->tenant_id,
                        'client_id' => $this->record->client_id,
                        'total_amount' => $this->record->total_amount,
                    ]);

                    // Atualiza o status do orçamento
                    $this->record->update(['status' => 'converted']);

                    $this->notify('success', 'Orçamento convertido em pedido com sucesso!');
                }),

            Actions\Action::make('reactivate')
                ->label('Reativar')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->status === 'expired')
                ->action(function () {
                    $this->record->update([
                        'status' => 'open',
                        'valid_until' => now()->addDays(7),
                    ]);
                    $this->notify('success', 'Orçamento reativado com sucesso!');
                }),

            Actions\DeleteAction::make(),
        ];
    }
} 