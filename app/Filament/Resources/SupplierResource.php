<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use App\Models\State;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Exports\SupplierExport;
use Filament\Tables\Actions\ExportAction;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Cadastros';
    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('filament-panels.resources.labels.Supplier');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-panels.resources.labels.Suppliers');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informações Básicas')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nome')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\TextInput::make('contact_info')
                                ->label('Contato')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('email')
                                ->label('E-mail')
                                ->email()
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('phone')
                                ->label('Telefone')
                                ->tel()
                                ->mask('(99) 99999-9999')
                                ->required(),
                        ]),
                ]),

            Forms\Components\Section::make('Endereço')
                ->schema([
                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\TextInput::make('postal_code')
                                ->label('CEP')
                                ->required()
                                ->mask('99999-999'),

                            Forms\Components\TextInput::make('address')
                                ->label('Endereço')
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('neighborhood')
                                ->label('Bairro')
                                ->required(),
                        ]),

                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\Select::make('state_id')
                                ->label('Estado')
                                ->options(State::query()->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->required(),

                            Forms\Components\Select::make('city_id')
                                ->label('Cidade')
                                ->options(function (callable $get) {
                                    $state = $get('state_id');
                                    if (!$state) {
                                        return [];
                                    }
                                    return City::query()
                                        ->where('state_id', $state)
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->disabled(fn(callable $get) => !$get('state_id')),
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

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('Cidade')
                    ->sortable(),

                Tables\Columns\TextColumn::make('state.name')
                    ->label('Estado')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('name')
                    ->label('Nome')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $name): Builder => $query->where('name', 'like', "%{$name}%"),
                        );
                    }),

                Tables\Filters\Filter::make('email')
                    ->label('E-mail')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['email'],
                            fn (Builder $query, $email): Builder => $query->where('email', 'like', "%{$email}%"),
                        );
                    }),

                Tables\Filters\Filter::make('phone')
                    ->label('Telefone')
                    ->form([
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->mask('(99) 99999-9999'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['phone'],
                            fn (Builder $query, $phone): Builder => $query->where('phone', 'like', "%{$phone}%"),
                        );
                    }),

                Tables\Filters\SelectFilter::make('state')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Estado'),

                Tables\Filters\SelectFilter::make('city')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Cidade'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar Relatório')
                    ->color(fn (ExportAction $action) => $action->isDisabled() ? 'gray' : 'success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->exporter(SupplierExport::class)
                    ->disabled(fn () => Supplier::query()->count() === 0)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('tenant-admin')) {
            return $query;
        }

        return $query->where('tenant_id', $user->getTenantId());
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['tenant-admin']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole(['tenant-admin']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if ($user->hasRole('tenant-admin')) {
            return true;
        }

        return $user->hasRole('tenant-admin') && $record->tenant_id === $user->getTenantId();
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        if ($user->hasRole('tenant-admin')) {
            return true;
        }

        return $user->hasRole('tenant-admin') && $record->tenant_id === $user->getTenantId();
    }
}
