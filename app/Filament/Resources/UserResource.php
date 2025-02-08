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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return true; // Permite que todos vejam a listagem (será filtrada depois)
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user->hasRole('super-admin') ||
            $user->hasRole('tenant-admin');
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

        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('super-admin')) {
            // Super admin vê todos os usuários
            return $query->withCount('tenantUsers');
        }

        if ($user->hasRole('tenant-admin')) {
            // Tenant admin vê apenas seus usuários
            return $query->where('tenant_id', $user->id);
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

                    Forms\Components\Select::make('roles')
                        ->multiple()
                        ->relationship(
                            'roles',
                            'name',
                            function ($query) use ($user) {
                                if ($user->hasRole('tenant-admin')) {
                                    return $query->whereIn('name', ['manager', 'operator', 'client']);
                                }
                                return $query;
                            }
                        )
                        ->preload()
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
        ];

        // Apenas super admin vê contagem de usuários
        if ($user->hasRole('super-admin')) {
            $columns[] = Tables\Columns\TextColumn::make('tenantUsers_count')
                ->label('Usuários')
                ->counts('tenantUsers');
        }

        return $table
            ->columns($columns)
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn(Model $record) => $record->is_tenant_admin),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if (!$record->is_tenant_admin) {
                                    $record->delete();
                                }
                            });
                        }),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar Relatório')
                    ->color(fn (ExportAction $action) => $action->isDisabled() ? 'gray' : 'success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->exporter(UserExport::class)
                    ->disabled(fn () => User::query()->count() === 0)
            ]);
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
        if (!$user->hasRole(['super-admin', 'tenant-admin'])) {
            return [
                NavigationItem::make('Meu Perfil')
                    ->icon('heroicon-o-user')
                    ->label('Meu Perfil')
                    ->url(fn() => static::getUrl('edit', ['record' => $user->id]))
                    ->group('Minha Conta')
                    ->sort(1),
            ];
        }

        // Para admins, mostra a navegação padrão do resource
        return parent::getNavigationItems();
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();

        // Super admin e tenant admin podem ver qualquer usuário
        if ($user->hasRole(['super-admin', 'tenant-admin'])) {
            return true;
        }

        // Outros usuários só podem ver seu próprio perfil
        return $user->id === $record->id;
    }
}
