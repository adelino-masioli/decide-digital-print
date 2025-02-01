<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Order;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuoteMail;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('filament-panels.resources.labels.Quote');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-panels.resources.labels.Quotes');
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        return $form->schema([
            Forms\Components\Section::make('Informações Básicas')
                ->schema([

                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('number')
                                ->label('Número')
                                ->default(fn() => 'ORC-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT))
                                ->disabled()
                                ->dehydrated()
                                ->required(),


                            Forms\Components\DatePicker::make('valid_until')
                                ->label('Válido até')
                                ->default(now()->addDays(7))
                                ->required(),

                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'draft' => trans('filament-panels.resources.status.quotes.draft'),
                                    'open' => trans('filament-panels.resources.status.quotes.open'),
                                    'approved' => trans('filament-panels.resources.status.quotes.approved'),
                                    'expired' => trans('filament-panels.resources.status.quotes.expired'),
                                    'converted' => trans('filament-panels.resources.status.quotes.converted'),
                                    'canceled' => trans('filament-panels.resources.status.quotes.canceled'),
                                ])
                                ->default('draft')
                                ->required(),
                        ]),

                    Forms\Components\Select::make('client_id')
                        ->label('Cliente')
                        ->relationship(
                            'client',
                            'name',
                            fn(Builder $query) => $query
                                ->whereHas('roles', fn($q) => $q->where('name', 'client'))
                                ->when(
                                    !auth()->user()->hasRole('super-admin'),
                                    fn($q) => $q->where('tenant_id', auth()->user()->getTenantId())
                                )
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Textarea::make('notes')
                        ->label('Observações')
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Vendedor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Valor Total')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Válido até')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => ['draft', 'open'],
                        'success' => 'approved',
                        'danger' => ['expired', 'canceled'],
                        'primary' => 'converted',
                    ])
                    ->formatStateUsing(fn (string $state): string => trans("filament-panels.resources.status.quotes.{$state}")),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => trans('filament-panels.resources.status.quotes.draft'),
                        'open' => trans('filament-panels.resources.status.quotes.open'),
                        'approved' => trans('filament-panels.resources.status.quotes.approved'),
                        'expired' => trans('filament-panels.resources.status.quotes.expired'),
                        'converted' => trans('filament-panels.resources.status.quotes.converted'),
                        'canceled' => trans('filament-panels.resources.status.quotes.canceled'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->canBeApproved())
                    ->action(function ($record, $livewire) {
                        $record->update(['status' => 'approved']);
                        Notification::make()
                            ->success()
                            ->title('Orçamento aprovado com sucesso!')
                            ->send();
                    }),

                Tables\Actions\Action::make('convert')
                    ->label('Converter')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->canBeConverted())
                    ->action(function ($record, $livewire) {
                        Order::create([
                            'number' => 'PED-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT),
                            'quote_id' => $record->id,
                            'tenant_id' => $record->tenant_id,
                            'client_id' => $record->client_id,
                            'total_amount' => $record->total_amount,
                        ]);

                        $record->update(['status' => 'converted']);
                        Notification::make()
                            ->success()
                            ->title('Orçamento convertido em pedido com sucesso!')
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('pdf')
                    ->icon('heroicon-o-document-arrow-down')
                    ->label('PDF')
                    ->action(function (Quote $record) {
                        $pdf = Pdf::loadView('pdf.quote', ['quote' => $record]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "orcamento_{$record->id}.pdf"
                        );
                    }),
                
                Action::make('email')
                    ->icon('heroicon-o-envelope')
                    ->label('Enviar Email')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                    ])
                    ->action(function (Quote $record, array $data) {
                        $pdf = Pdf::loadView('pdf.quote', ['quote' => $record]);
                        $pdfPath = storage_path("app/public/orcamento_{$record->id}.pdf");
                        $pdf->save($pdfPath);

                        Mail::to($data['email'])->send(new QuoteMail($record, $pdfPath));

                        unlink($pdfPath);

                        Notification::make()
                            ->title('Email enviado com sucesso!')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\QuoteItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
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
}
