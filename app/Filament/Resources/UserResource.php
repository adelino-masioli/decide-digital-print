<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Filament\Navigation\NavigationItem;
use App\Models\State;
use App\Models\City;
use Filament\Support\RawJs;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\Rule;
use App\Filament\Exports\UserExport;
use Filament\Tables\Actions\ExportAction;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        return true; // Permite que todos vejam a listagem (será filtrada depois)
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user->hasRole('super-admin') ||
            $user->hasRole('tenant-admin') || $user->hasRole('manager');
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        // Super admin pode editar qualquer usuário
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Tenant admin pode editar usuários do seu tenant
        if ($user->hasRole('tenant-admin') && $record->tenant_id === $user->id) {
            return true;
        }

        if ($user->hasRole('manager') && $record->tenant_id === $user->tenant_id) {
            return true;
        }

        if ($user->hasRole('manager') && $record->is_tenant_admin === false) {
            return true;
        }
        

        // Outros usuários só podem editar seu próprio perfil
        return $user->id === $record->id;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        // Ninguém pode deletar um tenant admin
        if ($record->is_tenant_admin) {
            return false;
        }

        // Super admin pode deletar qualquer usuário (exceto tenant admin)
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Tenant admin pode deletar usuários do seu tenant
        if ($user->hasRole('tenant-admin') && $record->tenant_id === $user->id) {
            return true;
        }

        if ($user->hasRole('manager') && $record->tenant_id === $user->tenant_id) {
            return true;
        }

        if ($user->hasRole('manager') && $record->is_tenant_admin === false) {
            return true;
        }

        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('super-admin')) {
            return $query->withoutGlobalScopes()
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['super-admin', 'tenant-admin']);
                })
                ->withCount('tenantUsers');
        }
    
        if ($user->hasRole('tenant-admin')) {
            // Tenant admin vê apenas seus usuários
            return $query->whereHas('roles', function ($query) {
                //$query->whereNotIn('name', ['super-admin', 'tenant-admin']);
            })->where('tenant_id', $user->id);
        }

        if ($user->hasRole('manager')) {
            // Tenant admin vê apenas seus usuários
            return $query->whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['super-admin', 'tenant-admin']);
            })->where('tenant_id', $user->tenant_id);
        }

        // Outros usuários veem apenas seu próprio perfil
        return $query->where('id', $user->id);
    }

    public static function getModelLabel(): string
    {
        return trans('filament-panels.resources.labels.User');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('filament-panels.resources.labels.Users');
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $record = $form->getRecord();
        $isCreating = $form->getOperation() === 'create';
        $isOwnProfile = $record && $record->id === $user->id;

        return $form->schema([
            Forms\Components\Section::make('Informações Básicas')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label(trans('filament-panels.resources.fields.first_name.label'))
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('last_name')
                                ->label(trans('filament-panels.resources.fields.last_name.label'))
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
                                ->unique(ignoreRecord: true)
                                ->visible(!$isOwnProfile || $user->hasRole(['super-admin', 'tenant-admin'])),

                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->required($isCreating)
                                ->dehydrateStateUsing(fn($state) => $state ? Hash::make($state) : null)
                                ->dehydrated(fn($state) => filled($state))
                                ->label($isCreating ? 'Senha' : 'Nova Senha (deixe em branco para manter a atual)'),
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
                                ->mask('99999-999')
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (empty($state)) return;
                                    
                                    $cep = preg_replace('/[^0-9]/', '', $state);
                                    if (strlen($cep) !== 8) return;
                                    
                                    $response = Http::get("https://viacep.com.br/ws/{$cep}/json/")->json();
                                    
                                    if (!isset($response['erro'])) {
                                        $set('street', $response['logradouro']);
                                        $set('neighborhood', $response['bairro']);
                                        $set('complement', $response['complemento']);
                                    
                                        // Find state and city
                                        $state = State::where('uf', $response['uf'])->first();
                                        if ($state) {
                                            $set('state_id', $state->id);
                                            
                                            $city = City::where('state_id', $state->id)
                                                ->where('name', $response['localidade'])
                                                ->first();
                                                
                                            if ($city) {
                                                $set('city_id', $city->id);
                                            }
                                        }
                                    }
                                }),
                        
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
                        ->columnSpanFull()
                ]),

            Forms\Components\Section::make('Informações Adicionais')
                ->schema([
                    Forms\Components\FileUpload::make('company_logo')
                        ->label('Logo da Empresa')
                        ->directory('logos')
                        ->nullable(),
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('company_name')
                                ->label('Nome da Empresa')
                                ->maxLength(255)
                                ->required(),

                            Forms\Components\TextInput::make('trading_name')
                                ->label('Nome Fantasia')
                                ->maxLength(255)
                                ->nullable(),

                            Forms\Components\TextInput::make('state_registration')
                                ->label('Inscrição Estadual')
                                ->maxLength(20)
                                ->nullable(),

                            Forms\Components\TextInput::make('municipal_registration')
                                ->label('Inscrição Municipal')
                                ->maxLength(20)
                                ->nullable(),

                            Forms\Components\TextInput::make('company_address')
                                ->label('Endereço da Empresa')
                                ->maxLength(255)
                                ->required(),



                            Forms\Components\TextInput::make('company_latitude')
                                ->label('Latitude')
                                ->numeric()
                                ->nullable(),

                            Forms\Components\TextInput::make('company_longitude')
                                ->label('Longitude')
                                ->numeric()
                                ->nullable(),


                        ]),
                    Forms\Components\Textarea::make('seo_text')
                        ->label('Texto descritivo para SEO')
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->visible(fn() => $record?->is_tenant_admin || ($isCreating && $user->hasRole('super-admin'))),

            // Campos administrativos (apenas para super-admin e tenant-admin)
            Forms\Components\Section::make('Configurações')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->label('Ativo')
                                ->required()
                                ->disabled(function () use ($user, $record) {
                                    if ($record && $record->id === $user->id && $user->is_tenant_admin) {
                                        return true;
                                    }
                                    return false;
                                }),

                            Forms\Components\Toggle::make('is_tenant_admin')
                                ->label('Admin da Gráfica')
                                ->required()
                                ->visible($user->hasRole('super-admin')),
                        ]),

                    Forms\Components\Select::make('role')
                        ->label('Permissões')
                        ->multiple()
                        ->relationship(
                            'roles',
                            'name',
                            function ($query) use ($user) {
                                if ($user->hasRole('tenant-admin')) {
                                    return $query->whereIn('name', ['manager', 'operator', 'client']);
                                }
                                if ($user->hasRole('super-admin')) {
                                    return $query->whereIn('name', ['super-admin', 'tenant-admin']);
                                }
                                return $query;
                            }
                        )
                        ->preload()
                        ->options(function () {
                            // Assumindo que você tem acesso ao usuário autenticado
                            if (auth()->user()->hasRole('super-admin')) {
                                return Role::query()
                                    ->whereIn('name', ['super-admin', 'tenant-admin'])
                                    ->pluck('id', 'name')
                                    ->mapWithKeys(function ($id, $name) {
                                        return [$id => match($name) {
                                            'super-admin' => 'Super Admin',
                                            'tenant-admin' => 'Admin da Gráfica'
                                        }];
                                    })
                                    ->toArray();
                            } else {
                                return Role::query()
                                    ->whereIn('name', ['manager', 'operator', 'client'])
                                    ->pluck('id', 'name')
                                    ->mapWithKeys(function ($id, $name) {
                                        return [$id => match($name) {
                                            'manager' => 'Gerente',
                                            'operator' => 'Operador',
                                            'client' => 'Cliente'
                                        }];
                                    })
                                    ->toArray();
                            }
                        })
                        ->searchable()
                        ->disabled(function () use ($user, $record) {
                            if ($record && $record->id === $user->id && $user->is_tenant_admin) {
                                return true;
                            }
                            return false;
                        }),
                ])
                ->visible($user->hasRole(['super-admin', 'tenant-admin']) && (!$isOwnProfile || $user->hasRole('super-admin'))),
        ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        
        $columns = [
            Tables\Columns\TextColumn::make('name')
                ->label(trans('filament-panels.resources.table.columns.first_name'))
                ->searchable(),

            Tables\Columns\TextColumn::make('last_name')
                ->label(trans('filament-panels.resources.table.columns.last_name'))
                ->searchable(),

            Tables\Columns\TextColumn::make('email')
                ->label(trans('filament-panels.resources.table.columns.email'))
                ->searchable(),

            Tables\Columns\TextColumn::make('document')
                ->label(trans('filament-panels.resources.table.columns.document'))
                ->searchable(),

            Tables\Columns\TextColumn::make('phone')
                ->label(trans('filament-panels.resources.table.columns.phone'))
                ->searchable(),

            Tables\Columns\TextColumn::make('address.state.name')
                ->label('Estado')
                ->sortable(),

            Tables\Columns\TextColumn::make('address.city.name')
                ->label('Cidade')
                ->sortable(),

            Tables\Columns\IconColumn::make('is_active')
                ->label(trans('filament-panels.resources.table.columns.is_active'))
                ->boolean()
                ->disabled(function (Model $record) use ($user) {
                    return $record->id === $user->id && $user->is_tenant_admin;
                }),

            Tables\Columns\TextColumn::make('roles.name')
                ->label('Tipo de Usuário')
                ->formatStateUsing(function ($state) {
                    return match($state) {
                        'super-admin' => 'Super Admin',
                        'tenant-admin' => 'Admin da Gráfica',
                        'manager' => 'Gerente',
                        'operator' => 'Operador',
                        'client' => 'Cliente',
                        default => $state
                    };
                })
                ->visible($user->hasRole('super-admin')),
        ];

        // Apenas super admin vê contagem de usuários
        if ($user->hasRole('super-admin')) {
            $columns[] = Tables\Columns\TextColumn::make('tenantUsers_count')
                ->label('Usuários')
                ->counts('tenantUsers')
                ->visible($user->hasRole('super-admin'));
        }

        return $table
            ->columns($columns)
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

                Tables\Filters\SelectFilter::make('role')
                    ->label('Tipo de Usuário')
                    ->options([
                        'super-admin' => 'Super Admin',
                        'tenant-admin' => 'Admin da Gráfica',
                        'manager' => 'Gerente',
                        'operator' => 'Operador',
                        'client' => 'Cliente',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $role): Builder => 
                                $query->whereHas('roles', fn ($q) => $q->where('name', $role))
                        );
                    })
                    ->visible($user->hasRole('super-admin')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn(Model $record) => 
                        $record->is_tenant_admin || 
                        $record->hasRole('super-admin')
                    )
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if (!$record->is_tenant_admin && !$record->hasRole('super-admin')) {
                                    $record->delete();
                                }
                            });
                        }),
                ]),
            ])
            ->headerActions(
                auth()->user()->hasAnyRole(['manager', 'tenant-admin'])
                    ? [
                        ExportAction::make()
                            ->label('Exportar Relatório')
                            ->color(fn (ExportAction $action) => $action->isDisabled() ? 'gray' : 'success')
                            ->icon('heroicon-o-document-arrow-down')
                            ->exporter(UserExport::class)
                            ->disabled(function () {
                                $user = auth()->user();
                                return User::query()
                                    ->where('tenant_id', $user->tenant_id)
                                    ->count() === 0;
                            })
                    ]
                    : []
            );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationItems(): array
    {
        $user = auth()->user();
   
        // Se for um usuário comum (não admin), só mostra o link do perfil
        if (!$user->hasRole(['super-admin', 'tenant-admin', 'manager'])) {
            return [
                NavigationItem::make('Meu Perfil')
                    ->icon('heroicon-o-user')
                    ->label('Meu Perfil')
                    ->url(fn() => static::getUrl('edit', ['record' => $user->id]))
                    ->group('Minha Conta')
                    ->sort(5),
            ];
        }

        // Para tenant-admin, mostra a navegação padrão do resource
        return parent::getNavigationItems();
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();

        // Super admin e tenant admin podem ver qualquer usuário
        if ($user->hasRole(['super-admin', 'tenant-admin', 'manager'])) {
            return true;
        }

        // Outros usuários só podem ver seu próprio perfil
        return $user->id === $record->id;
    }
}
