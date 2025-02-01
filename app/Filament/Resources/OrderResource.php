<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\RelationManagers;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;
use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('filament-panels.resources.labels.Order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-panels.resources.labels.Orders');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informações do Pedido')
                ->schema([
                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\TextInput::make('number')
                                ->label('Número')
                                ->disabled(),

                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    Order::STATUS_PENDING_PAYMENT => trans('filament-panels.resources.status.orders.pending_payment'),
                                    Order::STATUS_PROCESSING => trans('filament-panels.resources.status.orders.processing'),
                                    Order::STATUS_IN_PRODUCTION => trans('filament-panels.resources.status.orders.in_production'),
                                    Order::STATUS_COMPLETED => trans('filament-panels.resources.status.orders.completed'),
                                    Order::STATUS_DELIVERED => trans('filament-panels.resources.status.orders.delivered'),
                                    Order::STATUS_CANCELED => trans('filament-panels.resources.status.orders.canceled'),
                                ])
                                ->required(),

                            Forms\Components\Select::make('payment_method')
                                ->label('Forma de Pagamento')
                                ->options([
                                    'cash' => 'Dinheiro',
                                    'credit_card' => 'Cartão de Crédito',
                                    'pix' => 'PIX',
                                    'bank_slip' => 'Boleto',
                                ])
                                ->required(),

                            Forms\Components\Select::make('payment_status')
                                ->label('Status do Pagamento')
                                ->options([
                                    Order::PAYMENT_STATUS_PENDING => 'Pendente',
                                    Order::PAYMENT_STATUS_PAID => 'Pago',
                                    Order::PAYMENT_STATUS_FAILED => 'Falhou',
                                    Order::PAYMENT_STATUS_REFUNDED => 'Reembolsado',
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state === Order::PAYMENT_STATUS_PAID) {
                                        $set('status', Order::STATUS_PROCESSING);
                                    }
                                }),

                            Forms\Components\TextInput::make('total_amount')
                                ->label('Valor Total')
                                ->disabled()
                                ->prefix('R$'),
                        ]),
                    Forms\Components\Textarea::make('notes')
                        ->label('Observações')
                        ->rows(3),
                ]),

            Forms\Components\Section::make('Informações do Orçamento')
                ->schema([
                    Forms\Components\Placeholder::make('quote.number')
                        ->label('Número do Orçamento')
                        ->content(fn ($record) => $record->quote->number),

                    Forms\Components\Placeholder::make('quote.client.name')
                        ->label('Cliente')
                        ->content(fn ($record) => $record->quote->client->name),

                    Forms\Components\Placeholder::make('quote.seller.name')
                        ->label('Vendedor')
                        ->content(fn ($record) => $record->quote->seller->name),
                ])
                ->columns(3),
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

                Tables\Columns\TextColumn::make('quote.client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Valor Total')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => [Order::STATUS_PENDING_PAYMENT],
                        'primary' => Order::STATUS_PROCESSING,
                        'primary' => Order::STATUS_IN_PRODUCTION,
                        'success' => [Order::STATUS_COMPLETED, Order::STATUS_DELIVERED],
                        'danger' => Order::STATUS_CANCELED,
                    ])
                    ->formatStateUsing(fn (string $state): string => trans("filament-panels.resources.status.orders.{$state}")),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Pagamento')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => ['failed', 'refunded'],
                    ]),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Forma de Pagamento')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Dinheiro',
                        'credit_card' => 'Cartão de Crédito',
                        'pix' => 'PIX',
                        'bank_slip' => 'Boleto',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Order::STATUS_PENDING_PAYMENT => trans('filament-panels.resources.status.orders.pending_payment'),
                        Order::STATUS_PROCESSING => trans('filament-panels.resources.status.orders.processing'),
                        Order::STATUS_IN_PRODUCTION => trans('filament-panels.resources.status.orders.in_production'),
                        Order::STATUS_COMPLETED => trans('filament-panels.resources.status.orders.completed'),
                        Order::STATUS_DELIVERED => trans('filament-panels.resources.status.orders.delivered'),
                        Order::STATUS_CANCELED => trans('filament-panels.resources.status.orders.canceled'),
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pendente',
                        'paid' => 'Pago',
                        'failed' => 'Falhou',
                        'refunded' => 'Reembolsado',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Action::make('pdf')
                    ->icon('heroicon-o-document-arrow-down')
                    ->label('PDF')
                    ->action(function (Order $record) {
                        $pdf = Pdf::loadView('pdf.order', ['order' => $record]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "pedido_{$record->id}.pdf"
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
                    ->action(function (Order $record, array $data) {
                        $pdf = Pdf::loadView('pdf.order', ['order' => $record]);
                        $pdfPath = storage_path("app/public/pedido_{$record->id}.pdf");
                        $pdf->save($pdfPath);

                        Mail::to($data['email'])->send(new OrderMail($record, $pdfPath));

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
            RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
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