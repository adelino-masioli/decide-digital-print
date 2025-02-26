<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpportunityResource\Pages;
use App\Models\Opportunity;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\View;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Filament\Exports\OpportunityExport;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'CRM';
    
    // Adicionando os labels em português
    protected static ?string $modelLabel = 'Oportunidade';
    protected static ?string $pluralModelLabel = 'Oportunidades';
    protected static ?int $navigationSort = 3; 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->description('Registre os detalhes iniciais do pedido e defina os responsáveis pelo atendimento')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título do Projeto')
                            ->placeholder('Ex: Banner Feira de Negócios - Cliente ABC')
                            ->helperText('Descreva brevemente o tipo de material gráfico e sua finalidade')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('client_id')
                            ->label('Cliente')
                            ->relationship(
                                'client',
                                'name',
                                fn (Builder $query) => $query
                                    ->where('tenant_id', Auth::user()->tenant_id)
                                    ->whereHas('roles', fn ($q) => $q->where('name', 'client'))
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\Select::make('responsible_id')
                            ->label('Atendente Responsável')
                            ->relationship(
                                'responsible',
                                'name',
                                fn (Builder $query) => $query
                                    ->where('tenant_id', Auth::user()->tenant_id)
                                    ->whereHas('roles', fn ($q) => $q->whereIn('name', ['manager', 'operator']))
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Detalhes do Projeto')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Especificações do Projeto')
                            ->placeholder('Descreva os detalhes do material: dimensões, cores, acabamentos, quantidade, etc.')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'lead' => 'Primeiro Contato',
                                'negotiation' => 'Em Orçamento',
                                'proposal' => 'Orçamento Enviado',
                                'won' => 'Pedido Fechado',
                                'lost' => 'Não Aprovado',
                            ])
                            ->required(),

                        Forms\Components\DatePicker::make('expected_closure_date')
                            ->label('Previsão de Fechamento')
                            ->helperText('Data prevista para aprovação do orçamento')
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->locale('pt_BR')
                            ->closeOnDateSelection()
                            ->native(false)
                            ->icon('heroicon-o-calendar')
                            ->prefixIcon('heroicon-o-calendar'),
                    ])->columns(2),

                // Seção do valor total
                Section::make()
                    ->schema([
                        View::make('opportunity-total-value')
                            ->viewData([
                                'opportunityId' => request()->segment(3)
                            ])
                            ->columnSpanFull()
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Projeto')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                    
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'lead' => 'Primeiro Contato',
                        'negotiation' => 'Em Orçamento',
                        'proposal' => 'Orçamento Enviado',
                        'won' => 'Pedido Fechado',
                        'lost' => 'Não Aprovado',
                    ])
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('expected_closure_date')
                    ->label('Data Prevista')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'lead' => 'Primeiro Contato',
                        'negotiation' => 'Em Orçamento',
                        'proposal' => 'Orçamento Enviado',
                        'won' => 'Pedido Fechado',
                        'lost' => 'Não Aprovado',
                    ]),
                    
                Tables\Filters\SelectFilter::make('responsible')
                    ->label('Responsável')
                    ->relationship('responsible', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions(
                Gate::check('manage-opportunities') 
                    ? [Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ])]
                    : []
            )
            ->headerActions(
                Gate::check('manage-opportunities')
                    ? [
                        ExportAction::make()
                            ->label('Exportar Relatório')
                            ->color(fn (ExportAction $action) => $action->isDisabled() ? 'gray' : 'success')
                            ->icon('heroicon-o-document-arrow-down')
                            ->exporter(OpportunityExport::class)
                            ->disabled(function () {
                                $user = Auth::user();
                                return Opportunity::query()
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
            OpportunityResource\RelationManagers\ItemsRelationManager::class,
            OpportunityResource\RelationManagers\InteractionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpportunities::route('/'),
            'create' => Pages\CreateOpportunity::route('/create'),
            'edit' => Pages\EditOpportunity::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Gate::check('opportunity.list');
    }

    public static function canCreate(): bool
    {
        return Gate::check('opportunity.create');
    }

    public static function canEdit(Model $record): bool
    {
        return Gate::check('opportunity.edit');
    }

    public static function canDelete(Model $record): bool
    {
        return Gate::check('opportunity.delete');
    }
} 