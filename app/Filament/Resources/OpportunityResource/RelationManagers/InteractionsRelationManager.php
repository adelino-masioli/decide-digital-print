<?php

namespace App\Filament\Resources\OpportunityResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Opportunity;

class InteractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interactions';
    protected static ?string $title = 'Interações';
    
    protected static ?string $recordTitleAttribute = 'type';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Tipo de Interação')
                    ->options([
                        'call' => 'Ligação',
                        'email' => 'E-mail',
                        'meeting' => 'Reunião',
                        'note' => 'Anotação',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('scheduled_at')
                    ->label('Agendado para')
                    ->helperText('Data prevista para agendamento da interação')
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->locale('pt_BR')
                    ->closeOnDateSelection()
                    ->native(false)
                    ->icon('heroicon-o-calendar')
                    ->prefixIcon('heroicon-o-calendar'),

                Forms\Components\DatePicker::make('completed_at')
                    ->label('Concluído em')
                    ->helperText('Data prevista para conclusão da interação')
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->locale('pt_BR')
                    ->closeOnDateSelection()
                    ->native(false)
                    ->icon('heroicon-o-calendar')
                    ->prefixIcon('heroicon-o-calendar'),


                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),

                Forms\Components\Hidden::make('client_id')
                    ->default(fn () => Opportunity::find($this->ownerRecord->id)->client_id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'warning' => 'call',
                        'success' => 'email',
                        'primary' => 'meeting',
                        'gray' => 'note',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'call' => 'Ligação',
                        'email' => 'E-mail',
                        'meeting' => 'Reunião',
                        'note' => 'Anotação',
                    }),
                    
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'call' => 'Ligação',
                        'email' => 'E-mail',
                        'meeting' => 'Reunião',
                        'note' => 'Anotação',
                    ]),
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
                Tables\Actions\CreateAction::make()
                    ->label('Nova Interação')
                    ->modalHeading('Adicionar Nova Interação'),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nova Interação'),
            ]);
    }
} 