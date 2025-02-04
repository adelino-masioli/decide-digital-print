<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplyResource\Pages;
use App\Models\Supply;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Cadastros';
    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('filament-panels.resources.labels.Supplies');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-panels.resources.labels.Supplies');
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        return $form->schema([
            Forms\Components\Section::make('Informações do Insumo')
                ->schema([
                    Forms\Components\Grid::make(5)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nome')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),

                            Forms\Components\Select::make('supplier_id')
                                ->label('Fornecedor')
                                ->relationship(
                                    'supplier',
                                    'name',
                                    fn(Builder $query) => $user->hasRole('super-admin')
                                        ? $query
                                        : $query->where('tenant_id', $user->getTenantId())
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\Select::make('unit')
                                ->label('Unidade')
                                ->options([
                                    'UN' => 'Unidade',
                                    'KG' => 'Quilograma',
                                    'G' => 'Grama',
                                    'L' => 'Litro',
                                    'ML' => 'Mililitro',
                                    'M' => 'Metro',
                                    'CM' => 'Centímetro',
                                    'M²' => 'Metro Quadrado',
                                    'M³' => 'Metro Cúbico',
                                    'CX' => 'Caixa',
                                    'PCT' => 'Pacote',
                                    'ROL' => 'Rolo',
                                    'PAR' => 'Par',
                                    'DZ' => 'Dúzia',
                                ])
                                ->required()
                                ->searchable()

                        ]),


                    Forms\Components\Textarea::make('description')
                        ->label('Descrição')
                        ->rows(3),


                ]),

            Forms\Components\Section::make('Controle de Estoque')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('stock')
                                ->label('Estoque Atual')
                                ->numeric()
                                ->required(),

                            Forms\Components\TextInput::make('min_stock')
                                ->label('Estoque Mínimo')
                                ->numeric()
                                ->required(),

                            Forms\Components\TextInput::make('cost_price')
                                ->label('Preço de Custo')
                                ->numeric()
                                ->prefix('R$')
                                ->required(),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Fornecedor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit')
                    ->label('Unidade'),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Estoque')
                    ->numeric(2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('min_stock')
                    ->label('Estoque Mínimo')
                    ->numeric(2),

                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Preço de Custo')
                    ->money('BRL')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supplier')
                    ->relationship('supplier', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupplies::route('/'),
            'create' => Pages\CreateSupply::route('/create'),
            'edit' => Pages\EditSupply::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('super-admin')) {
            return $query;
        }

        return $query->where('tenant_id', $user->getTenantId());
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['super-admin', 'tenant-admin']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole(['super-admin', 'tenant-admin']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasRole('tenant-admin') && $record->tenant_id === $user->getTenantId();
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasRole('tenant-admin') && $record->tenant_id === $user->getTenantId();
    }
}
