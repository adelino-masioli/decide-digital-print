<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use App\Models\State;
use App\Models\City;
use Filament\Support\RawJs;
use Filament\Forms\Components\TextInput;
use App\Filament\Exports\ClientExport;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Model;

class ClientResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $navigationGroup = 'Administração';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('client.list');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('client.create');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('client.edit');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('client.delete');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->role('client');
        $user = auth()->user();

        if ($user->hasRole('super-admin')) {
            return $query;
        }

        if ($user->hasRole('tenant-admin')) {
            return $query->where('tenant_id', $user->id);
        }

        return $query->where('tenant_id', $user->tenant_id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informações Básicas')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nome')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('last_name')
                                ->label('Sobrenome')
                                ->required()
                                ->maxLength(255),


                            TextInput::make('document')
                                ->label('CPF/CNPJ')
                                ->required()
                                ->unique(
                                    table: 'users',
                                    column: 'document',
                                    ignoreRecord: true,
                                )
                                ->validationMessages([
                                    'unique' => 'Este documento já está cadastrado no sistema.',
                                ])
                                ->mask(RawJs::make(<<<'JS'
                                    $input.length > 14 ? '99.999.999/9999-99' : '999.999.999-99'
                                JS))
                                ->rule('cpf_ou_cnpj'),
                        ]),

                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('phone')
                                ->mask(RawJs::make(<<<'JS'
                                    $input.length >= 14 ? '(99)99999-9999' : '(99)9999-9999'
                                JS))
                                ->label(trans('filament-panels.resources.fields.phone.label'))
                                ->required()
                                ->tel()
                                ->maxLength(15),

                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),

                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->required(fn(string $context): bool => $context === 'create')
                                ->dehydrateStateUsing(fn($state) => $state ? Hash::make($state) : null)
                                ->dehydrated(fn($state) => filled($state))
                                ->label(fn(string $context): string => $context === 'create' ? 'Senha' : 'Nova Senha (deixe em branco para manter a atual)'),
                        ]),
                ]),

            Forms\Components\Fieldset::make('Endereço')
                ->relationship('address')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('zip_code')
                                ->label('CEP')
                                ->required()
                                ->mask('99999-999'),

                            Forms\Components\TextInput::make('street')
                                ->label('Rua')
                                ->required(),

                            Forms\Components\TextInput::make('number')
                                ->label('Número')
                                ->required(),

                            Forms\Components\TextInput::make('neighborhood')
                                ->label('Bairro')
                                ->required(),

                            Forms\Components\Select::make('state_id')
                                ->label('Estado')
                                ->options(State::query()->pluck('name', 'id'))
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(fn($state, callable $set) => $set('city_id', null))
                                ->required(),

                            Forms\Components\Select::make('city_id')
                                ->label('Cidade')
                                ->options(function (callable $get) {
                                    $stateId = $get('state_id');
                                    if (!$stateId) {
                                        return [];
                                    }
                                    return City::query()
                                        ->where('state_id', $stateId)
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->required()
                                ->disabled(fn(callable $get) => !$get('state_id')),
                        ]),

                    Forms\Components\TextInput::make('complement')
                        ->label('Complemento')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Configurações')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Ativo')
                        ->default(true),

                    Forms\Components\Hidden::make('tenant_id')
                        ->default(fn() => auth()->user()->hasRole('tenant-admin') ? auth()->id() : auth()->user()->tenant_id),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Sobrenome')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\TextColumn::make('document')
                    ->label('Documento')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('address.state.name')
                    ->label('Estado')
                    ->sortable(),

                Tables\Columns\TextColumn::make('address.city.name')
                    ->label('Cidade')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
     
                Tables\Filters\Filter::make('document')
                    ->label('Documento')
                    ->form([
                        Forms\Components\TextInput::make('document')
                            ->label('Documento')
                            ->mask(RawJs::make(<<<'JS'
                                $input.length > 14 ? '99.999.999/9999-99' : '999.999.999-99'
                            JS))
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['document'],
                            fn (Builder $query, $document): Builder => $query->where('document', 'like', "%{$document}%"),
                        );
                    }),

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

                Tables\Filters\Filter::make('last_name')
                    ->label('Sobrenome')
                    ->form([
                        Forms\Components\TextInput::make('last_name')
                            ->label('Sobrenome'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['last_name'],
                            fn (Builder $query, $lastName): Builder => $query->where('last_name', 'like', "%{$lastName}%"),
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
                            ->mask(RawJs::make(<<<'JS'
                                $input.length >= 14 ? '(99)99999-9999' : '(99)9999-9999'
                            JS)),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['phone'],
                            fn (Builder $query, $phone): Builder => $query->where('phone', 'like', "%{$phone}%"),
                        );
                    }),
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
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar Relatório')
                    ->color(fn (ExportAction $action) => $action->isDisabled() ? 'gray' : 'success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->exporter(ClientExport::class)
                    ->disabled(fn () => User::query()->role('client')->count() === 0)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
