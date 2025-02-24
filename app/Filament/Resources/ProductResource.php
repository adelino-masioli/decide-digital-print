<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Filament\Resources\ProductResource\RelationManagers;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use App\Filament\Exports\ProductExport;
use Filament\Tables\Actions\ExportAction;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Catálogo';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('product.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('product.create');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('product.edit');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('product.delete');
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->can('product.view');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', auth()->user()->tenant_id);
    }

    public static function getModelLabel(): string
    {
        return __('filament-panels.resources.labels.Product');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-panels.resources.labels.Products');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Gerado automaticamente do nome'),

                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Gerado automaticamente baseado na categoria'),

                        Forms\Components\Select::make('category_id')
                            ->label('Categoria')
                            ->relationship(
                                'category',
                                'name',
                                fn (Builder $query) => $query->where('tenant_id', auth()->user()->tenant_id)
                                    ->whereNull('parent_id')
                            )
                            ->required()
                            ->live()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('subcategory_id')
                            ->label('Subcategoria')
                            ->relationship(
                                'subcategory',
                                'name',
                                fn (Builder $query, $get) => $query->where('tenant_id', auth()->user()->tenant_id)
                                    ->where('parent_id', $get('category_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('category_id')),

                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Especificações')
                    ->schema([
                        Forms\Components\TextInput::make('format')
                            ->label('Formato')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('material')
                            ->label('Material')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('weight')
                            ->label('Peso')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('finishing')
                            ->label('Acabamento')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('color')
                            ->label('Cor')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('production_time')
                            ->label('Tempo de Produção (dias)')
                            ->numeric()
                            ->default(1),

                        Forms\Components\TextInput::make('min_quantity')
                            ->label('Quantidade Mínima')
                            ->numeric()
                            ->default(1),

                        Forms\Components\TextInput::make('max_quantity')
                            ->label('Quantidade Máxima')
                            ->numeric(),

                        Forms\Components\TextInput::make('base_price')
                            ->label('Preço Base')
                            ->numeric()
                            ->prefix('R$')
                            ->required(),

                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem')
                            ->image()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Personalização')
                    ->schema([
                        Forms\Components\KeyValue::make('customization_options')
                            ->label('Opções de Personalização')
                            ->keyLabel('Opção')
                            ->valueLabel('Valores')
                            ->addable()
                            ->reorderable(),

                        Forms\Components\KeyValue::make('file_requirements')
                            ->label('Requisitos do Arquivo')
                            ->keyLabel('Requisito')
                            ->valueLabel('Especificação')
                            ->addable()
                            ->reorderable(),
                    ])
                    ->columns(2),
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

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subcategory.name')
                    ->label('Subcategoria')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Preço Base')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('subcategory')
                    ->label('Subcategoria')
                    ->relationship('subcategory', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueLabel('Produtos Ativos')
                    ->falseLabel('Produtos Inativos')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions(
                auth()->user()->hasAnyRole(['manager', 'tenant-admin']) 
                    ? [Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ])]
                    : []
            )
            ->headerActions(
                auth()->user()->hasAnyRole(['manager', 'tenant-admin'])
                    ? [
                        ExportAction::make()
                            ->label('Exportar Relatório')
                            ->color(fn (ExportAction $action) => $action->isDisabled() ? 'gray' : 'success')
                            ->icon('heroicon-o-document-arrow-down')
                            ->exporter(ProductExport::class)
                            ->disabled(function () {
                                $user = auth()->user();
                                return Product::query()
                                    ->where('tenant_id', $user->tenant_id)
                                    ->count() === 0;
                            })
                    ]
                    : []
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SuppliesRelationManager::class,
        ];
    }
} 