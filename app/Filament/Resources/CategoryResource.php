<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Cadastros';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = null;
    protected static ?string $pluralModelLabel = null;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['super-admin', 'tenant-admin', 'manager']);
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

        return $user->hasRole('tenant-admin') && $record->tenant_id === $user->id;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasRole('tenant-admin') && $record->tenant_id === $user->id;
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
        return trans('filament-panels.resources.labels.Category');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('filament-panels.resources.labels.Categories');
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        return $form
            ->schema([
                TextInput::make('name')
                    ->label(trans('filament-panels.resources.fields.name.label'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true),

                Select::make('parent_id')
                    ->label('Categoria Pai')
                    ->relationship(
                        'parent',
                        'name',
                        function (Builder $query) use ($user) {
                            if (!$user->hasRole('super-admin')) {
                                $query->where('tenant_id', $user->getTenantId());
                            }
                            return $query;
                        }
                    )
                    ->searchable()
                    ->preload(),

               
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('description')
                ->label(trans('filament-panels.resources.fields.description.label'))
                    ->maxLength(65535)
                    ->columnSpanFull(),


                Toggle::make('is_active')
                    ->label(trans('filament-panels.resources.fields.is_active.label'))
                    ->default(true),

                TextInput::make('tenant_id')
                    ->hidden()
                    ->default(fn () => auth()->user()->getTenantId()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(trans('filament-panels.resources.fields.name.label'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_path')
                    ->label('Caminho Completo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                ->label(trans('filament-panels.resources.table.columns.is_active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
} 