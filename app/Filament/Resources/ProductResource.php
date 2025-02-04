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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Cadastros';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['super-admin', 'tenant-admin', 'manager']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole(['super-admin', 'tenant-admin', 'manager']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return ($user->hasRole(['tenant-admin', 'manager']) && $record->tenant_id === $user->getTenantId());
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return ($user->hasRole(['tenant-admin', 'manager']) && $record->tenant_id === $user->getTenantId());
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
        $user = auth()->user();

        return $form->schema([
            Forms\Components\Section::make('Informações Básicas')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label(__('filament-panels.resources.fields.name.label'))
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('sku')
                                ->label('SKU')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->columnSpan(1),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('category_id')
                                ->label('Categoria')
                                ->relationship(
                                    'category',
                                    'name',
                                    fn (Builder $query) => $user->hasRole('super-admin') 
                                        ? $query->parents() 
                                        : $query->parents()->where('tenant_id', $user->getTenantId())
                                )
                                ->required()
                                ->live()
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('subcategory_id')
                                ->label('Subcategoria')
                                ->options(function (callable $get) use ($user) {
                                    $categoryId = $get('category_id');
                                    if (!$categoryId) {
                                        return [];
                                    }

                                    $query = Category::query()->where('parent_id', $categoryId);
                                    if (!$user->hasRole('super-admin')) {
                                        $query->where('tenant_id', $user->getTenantId());
                                    }

                                    return $query->pluck('name', 'id')->toArray();
                                })
                                ->searchable()
                                ->preload()
                                ->disabled(fn (callable $get) => !$get('category_id')),
                        ]),

                    Forms\Components\TextInput::make('keywords')
                        ->label('Palavras-chave')
                        ->placeholder('Separadas por vírgula')
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('image')
                        ->label('Imagem')
                        ->image()
                        ->directory('public/products')
                        ->visibility('public')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Especificações Técnicas')
                ->schema([
                    Forms\Components\TextInput::make('format')
                        ->label('Formato')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('material')
                        ->label('Material')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('weight')
                        ->label('Gramatura')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('finishing')
                        ->label('Acabamento')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('color')
                        ->label('Cor')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('production_time')
                        ->label('Prazo de Produção (dias)')
                        ->numeric()
                        ->minValue(1),
                ])->columns(2),

            Forms\Components\Section::make('Opções de Personalização')
                ->schema([
                    Forms\Components\TextInput::make('min_quantity')
                        ->label('Quantidade Mínima')
                        ->numeric()
                        ->required()
                        ->default(1),

                    Forms\Components\TextInput::make('max_quantity')
                        ->label('Quantidade Máxima')
                        ->numeric(),

                    Forms\Components\Repeater::make('customization_options')
                        ->schema([
                            Forms\Components\TextInput::make('option')
                                ->label('Opção*')
                                ->required(),
                            Forms\Components\TextInput::make('value')
                                ->label('Valor*')
                                ->required(),
                        ])
                        ->columns(2),

                    Forms\Components\Repeater::make('file_requirements')
                        ->schema([
                            Forms\Components\TextInput::make('requirement')
                                ->label('Requisito*')
                                ->required(),
                            Forms\Components\TextInput::make('specification')
                                ->label('Especificação*')
                                ->required(),
                        ])
                        ->columns(2),
                ])->columns(2),

            Forms\Components\Section::make('Preços')
                ->schema([
                    Forms\Components\TextInput::make('base_price')
                        ->label('Preço Base')
                        ->numeric()
                        ->prefix('R$')
                        ->required()
                ])->columns(2),

            Forms\Components\Section::make('Descrição')
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('Descrição Completa')
                        ->rows(5)
                        ->columnSpanFull(),
                ]),

            
            Forms\Components\Toggle::make('is_active')
                ->label('Ativo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->disk('public')
                    ->square()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Preço Base')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo'),
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