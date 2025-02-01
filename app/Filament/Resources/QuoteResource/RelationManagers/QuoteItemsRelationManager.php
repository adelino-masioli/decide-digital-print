<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Itens do Orçamento';

    public function form(Form $form): Form
    {
        $user = auth()->user();

        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->label('Produto')
                ->relationship(
                    'product',
                    'name',
                    fn (Builder $query) => $user->hasRole('super-admin') 
                        ? $query 
                        : $query->where('tenant_id', $user->getTenantId())
                )
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $product = Product::find($state);
                        $set('unit_price', number_format($product->base_price, 2, ',', '.'));
                        $set('total_price', number_format($product->base_price, 2, ',', '.'));
                        $set('customization_options', $product->customization_options);
                        $set('file_requirements', $product->file_requirements);
                    }
                }),

            Forms\Components\TextInput::make('quantity')
                ->label('Quantidade')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->step(1)
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $unit_price = (float) str_replace(['.', ','], ['', '.'], $get('unit_price'));
                    $total = $state * $unit_price;
                    $set('total_price', number_format($total, 2, ',', '.'));
                }),

            Forms\Components\TextInput::make('unit_price')
                ->label('Preço Unitário')
                ->required()
                ->reactive()
                ->mask('999.999.999,99')
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $unit_price = (float) str_replace(['.', ','], ['', '.'], $state);
                    $quantity = (float) $get('quantity');
                    $total = $quantity * $unit_price;
                    $set('total_price', number_format($total, 2, ',', '.'));
                })
                ->dehydrateStateUsing(fn ($state) => (float) str_replace(['.', ','], ['', '.'], $state)),

            Forms\Components\TextInput::make('total_price')
                ->label('Preço Total')
                ->disabled()
                ->dehydrated()
                ->mask('999.999.999,99')
                ->dehydrateStateUsing(fn ($state) => (float) str_replace(['.', ','], ['', '.'], $state)),

            Forms\Components\KeyValue::make('customization_options')
                ->label('Opções de Personalização')
                ->keyLabel('Opção')
                ->valueLabel('Valor')
                ->reorderable(),

            Forms\Components\KeyValue::make('file_requirements')
                ->label('Requisitos do Arquivo')
                ->keyLabel('Requisito')
                ->valueLabel('Especificação')
                ->reorderable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Preço Unitário')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Preço Total')
                    ->money('BRL')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record, $data) {
                        // Atualiza o total do orçamento
                        $quote = $record->quote;
                        $quote->total_amount = $quote->items()->sum('total_price');
                        $quote->save();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record, $data) {
                        // Atualiza o total do orçamento
                        $quote = $record->quote;
                        $quote->total_amount = $quote->items()->sum('total_price');
                        $quote->save();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        // Atualiza o total do orçamento
                        $quote = $record->quote;
                        $quote->total_amount = $quote->items()->sum('total_price');
                        $quote->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            // Atualiza o total do orçamento
                            $this->getOwnerRecord()->update([
                                'total_amount' => $this->getOwnerRecord()->items()->sum('total_price')
                            ]);
                        }),
                ]),
            ]);
    }
} 