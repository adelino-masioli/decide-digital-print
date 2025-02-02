<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditOpportunity extends EditRecord
{
    protected static string $resource = OpportunityResource::class;

    // Traduzindo o título
    protected static ?string $title = 'Editar Oportunidade';

    // Adicionando descrição
    public function getSubheading(): ?string
    {
        return 'Atualize as informações do projeto, status do orçamento e registre as interações com o cliente.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('convertToPedido')
                ->label('Converter em Pedido')
                ->icon('heroicon-o-shopping-cart')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->status === 'won')
                ->action(function ($record) {
                    // Aqui você implementa a lógica de conversão
                    // Criar o pedido
                    // Copiar os itens
                    // Atualizar o status da oportunidade
                }),
            Actions\DeleteAction::make()
                ->label('Excluir'),
        ];
    }
} 