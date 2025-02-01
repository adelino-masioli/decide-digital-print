<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Itens do Pedido';
    protected static ?string $modelLabel = 'Item';
    protected static ?string $pluralModelLabel = 'Itens';

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

                Tables\Columns\TextColumn::make('customization_options')
                    ->label('Personalizações')
                    ->listWithLineBreaks()
                    ->bulleted(),

                Tables\Columns\TextColumn::make('file_requirements')
                    ->label('Requisitos do Arquivo')
                    ->listWithLineBreaks()
                    ->bulleted(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Removido ações pois os itens vêm do orçamento
            ])
            ->bulkActions([
                // Removido ações em massa
            ]);
    }
} 