<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Opportunity;

class LatestOpportunities extends BaseWidget
{
    protected static ?string $heading = 'Últimas Oportunidades';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Opportunity::latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
} 