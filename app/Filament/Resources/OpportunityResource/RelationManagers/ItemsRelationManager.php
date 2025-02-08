<?php

namespace App\Filament\Resources\OpportunityResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Product;
use Filament\Forms\Components\Repeater;
use App\Forms\Components\QuoteItemForm;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Livewire\Component;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Itens do Projeto';
    protected static ?string $createButtonLabel = 'Adicionar item à oportunidade';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                QuoteItemForm::make(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produto')
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qtd')
                    ->numeric(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Preço Unit.')
                    ->money('BRL')
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 2, ',', '.')),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->state(function ($record): float {
                        return $record->quantity * $record->unit_price;
                    })
                    ->money('BRL')
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 2, ',', '.'))
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 2, ',', '.')),
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar item à oportunidade')
                    ->modalHeading('Adicionar Produto ao Orçamento')
                    ->modalWidth('3xl')
                    ->after(function () {
                        $this->dispatch('items-changed');
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('duplicate')
                        ->label('Duplicar')
                        ->icon('heroicon-m-square-2-stack')
                        ->action(function ($record) {
                            $newItem = $record->replicate();
                            $newItem->push();
                        }),

                    Action::make('adjust_price')
                        ->label('Ajustar Preço')
                        ->icon('heroicon-m-currency-dollar')
                        ->form([
                            TextInput::make('unit_price')
                                ->label('Preço Unitário')
                                ->required()
                                ->prefix('R$')
                                ->numeric()
                                ->inputMode('decimal')
                                ->step('0.01')
                                ->default(fn ($record) => number_format($record->unit_price, 2, ',', '.')),
                        ])
                        ->action(function ($record, array $data) {
                            $unit_price = (float) str_replace(['R$', '.', ',', ' '], ['', '', '.', ''], $data['unit_price']);
                            $record->update([
                                'unit_price' => $unit_price,
                                'total_price' => $unit_price * $record->quantity,
                            ]);
                        }),

                    Action::make('adjust_quantity')
                        ->label('Ajustar Quantidade')
                        ->icon('heroicon-m-calculator')
                        ->form([
                            TextInput::make('quantity')
                                ->label('Quantidade')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->default(fn ($record) => $record->quantity),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'quantity' => $data['quantity'],
                                'total_price' => $record->unit_price * $data['quantity'],
                            ]);
                        }),

                    Tables\Actions\EditAction::make()
                        ->modalHeading('Editar Item do Orçamento')
                        ->modalWidth('3xl')
                        ->after(function () {
                            $this->dispatch('items-changed');
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->after(function () {
                            $this->dispatch('items-changed');
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Adicionar método para formatar valores antes de salvar
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $quantity = max(1, (int) ($data['quantity'] ?? 1));
        
        // Converte o preço unitário
        $unitPrice = is_string($data['unit_price']) 
            ? (float) str_replace(['.', ','], ['', '.'], $data['unit_price'])
            : (float) $data['unit_price'];
        
        // Calcula o total
        $totalPrice = $quantity * $unitPrice;

        return [
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
        ];
    }

    protected function afterCreate(): void
    {
        $this->dispatch('items-changed');
    }

    protected function afterSave(): void
    {
        $this->dispatch('items-changed');
    }

    protected function afterDelete(): void
    {
        $this->dispatch('items-changed');
    }
} 