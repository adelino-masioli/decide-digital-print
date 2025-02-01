<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Supply;
use Filament\Forms\Components\Select;

class SuppliesRelationManager extends RelationManager
{
    protected static string $relationship = 'supplies';
    protected static ?string $title = 'Insumos';
    protected static ?string $recordTitleAttribute = 'name';

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('quantity')
                ->label('Quantidade Necessária')
                ->helperText('Quanto deste insumo é necessário para produzir uma unidade do produto')
                ->numeric()
                ->required()
                ->minValue(0.01),

            Forms\Components\Select::make('unit')
                ->label('Unidade de Medida')
                ->options([
                    'un' => 'Unidade',
                    'ml' => 'Mililitro',
                    'l' => 'Litro',
                    'g' => 'Grama',
                    'kg' => 'Quilograma',
                    'cm' => 'Centímetro',
                    'm' => 'Metro',
                    'cm²' => 'Centímetro Quadrado',
                    'm²' => 'Metro Quadrado',
                    'folha' => 'Folha',
                ])
                ->required()
                ->helperText('Em qual unidade de medida a quantidade é expressa'),
        ];
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pivot.quantity')
                    ->label('Quantidade')
                    ->numeric(2),

                Tables\Columns\TextColumn::make('pivot.unit')
                    ->label('Unidade'),

                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Custo Unitário')
                    ->money('BRL'),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Fornecedor'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->modalWidth('lg')
                    ->recordSelectOptionsQuery(fn (Builder $query) => 
                        $user->hasRole('super-admin') 
                            ? $query 
                            : $query->where('tenant_id', $user->getTenantId())
                    )
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Selecione o Insumo')
                            ->helperText('Escolha o insumo necessário para este produto'),
                        ...$this->getFormSchema(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form($this->getFormSchema())
                    ->modalWidth('lg'),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
} 