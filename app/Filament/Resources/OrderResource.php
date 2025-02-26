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
use App\Filament\Exports\OrderExport;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
                                    'pending_payment' => trans('filament-panels.resources.status.orders.pending_payment'),
                                    'processing' => trans('filament-panels.resources.status.orders.processing'),
                                    'in_production' => trans('filament-panels.resources.status.orders.in_production'),
                                    'completed' => trans('filament-panels.resources.status.orders.completed'),
                                    'awaiting_pickup' => trans('filament-panels.resources.status.orders.awaiting_pickup'),
                                    'in_transit' => trans('filament-panels.resources.status.orders.in_transit'),
                                    'delivered' => trans('filament-panels.resources.status.orders.delivered'),
                                    'returned' => trans('filament-panels.resources.status.orders.returned'),
                                    'finished' => trans('filament-panels.resources.status.orders.finished'),
                                    'canceled' => trans('filament-panels.resources.status.orders.canceled'),
                                ])
                                ->default('pending_payment')
                                ->required(),

                            Forms\Components\Select::make('payment_method')
                                ->label('Método de Pagamento')
                                ->options([
                                    'cash' => trans('filament-panels.resources.status.payment_method.cash'),
                                    'credit_card' => trans('filament-panels.resources.status.payment_method.credit_card'),
                                    'pix' => trans('filament-panels.resources.status.payment_method.pix'),
                                    'bank_slip' => trans('filament-panels.resources.status.payment_method.bank_slip'),
                                ])
                                ->nullable(),

                            Forms\Components\Select::make('payment_status')
                                ->label('Status do Pagamento')
                                ->options([
                                    'pending' => trans('filament-panels.resources.status.payment_status.pending'),
                                    'paid' => trans('filament-panels.resources.status.payment_status.paid'),
                                    'failed' => trans('filament-panels.resources.status.payment_status.failed'),
                                    'refunded' => trans('filament-panels.resources.status.payment_status.refunded'),
                                ])
                                ->nullable(),

                            Forms\Components\TextInput::make('total_amount')
                                ->label('Valor Total')
                                ->disabled()
                                ->prefix('R$'),

                            Forms\Components\DatePicker::make('created_at')
                                ->label('Data da Venda')
                                ->default(now()->addDays(7))
                                ->displayFormat('d/m/Y | H:i:s')
                                ->format('Y-m-d H:i:s')
                                ->locale('pt_BR')
                                ->closeOnDateSelection()
                                ->native(false)
                                ->icon('heroicon-o-calendar')
                                ->prefixIcon('heroicon-o-calendar')
                                ->disabled(),

                            Forms\Components\DatePicker::make('updated_at')
                                ->label('Data da Atualização')
                                ->default(now()->addDays(7))
                                ->displayFormat('d/m/Y | H:i:s')
                                ->format('Y-m-d H:i:s')
                                ->locale('pt_BR')
                                ->closeOnDateSelection()
                                ->native(false)
                                ->icon('heroicon-o-calendar')
                                ->prefixIcon('heroicon-o-calendar')
                                ->disabled(),
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
                        'warning' => ['pending_payment'],
                        'primary' => ['processing', 'in_production'],
                        'success' => ['completed', 'delivered', 'finished'],
                        'info' => ['awaiting_pickup', 'in_transit'],
                        'danger' => ['canceled', 'returned'],
                    ])
                    ->formatStateUsing(fn (string $state): string => trans("filament-panels.resources.status.orders.{$state}")),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Pagamento')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => ['failed', 'refunded'],
                    ])
                    ->formatStateUsing(fn (string $state): string => trans("filament-panels.resources.status.payment_status.{$state}")),

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
                Tables\Filters\Filter::make('number')
                    ->label('Número')
                    ->form([
                        Forms\Components\TextInput::make('number')
                            ->label('Número'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['number'],
                            fn (Builder $query, $number): Builder => $query->where('number', 'like', "%{$number}%"),
                        );
                    }),

                Tables\Filters\SelectFilter::make('client')
                    ->relationship('quote.client', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Cliente'),

                Tables\Filters\Filter::make('total_amount')
                    ->label('Valor Total')
                    ->form([
                        Forms\Components\TextInput::make('min_amount')
                            ->label('Valor Mínimo')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_amount')
                            ->label('Valor Máximo')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn (Builder $query, $min): Builder => $query->where('total_amount', '>=', $min)
                            )
                            ->when(
                                $data['max_amount'],
                                fn (Builder $query, $max): Builder => $query->where('total_amount', '<=', $max)
                            );
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_payment' => trans('filament-panels.resources.status.orders.pending_payment'),
                        'processing' => trans('filament-panels.resources.status.orders.processing'),
                        'in_production' => trans('filament-panels.resources.status.orders.in_production'),
                        'completed' => trans('filament-panels.resources.status.orders.completed'),
                        'awaiting_pickup' => trans('filament-panels.resources.status.orders.awaiting_pickup'),
                        'in_transit' => trans('filament-panels.resources.status.orders.in_transit'),
                        'delivered' => trans('filament-panels.resources.status.orders.delivered'),
                        'returned' => trans('filament-panels.resources.status.orders.returned'),
                        'finished' => trans('filament-panels.resources.status.orders.finished'),
                        'canceled' => trans('filament-panels.resources.status.orders.canceled'),
                    ])
                    ->label('Status'),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => trans('filament-panels.resources.status.payment_status.pending'),
                        'paid' => trans('filament-panels.resources.status.payment_status.paid'),
                        'failed' => trans('filament-panels.resources.status.payment_status.failed'),
                        'refunded' => trans('filament-panels.resources.status.payment_status.refunded'),
                    ])
                    ->label('Status do Pagamento'),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => trans('filament-panels.resources.status.payment_method.cash'),
                        'credit_card' => trans('filament-panels.resources.status.payment_method.credit_card'),
                        'pix' => trans('filament-panels.resources.status.payment_method.pix'),
                        'bank_slip' => trans('filament-panels.resources.status.payment_method.bank_slip'),
                    ])
                    ->label('Método de Pagamento'),
            ])
            ->actions([
                Action::make('markAsPaid')
                    ->label('Marcar como Pago')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->payment_status === 'pending')
                    ->action(function (Order $record) {
                        $record->markAsPaid();
                        
                        Notification::make()
                            ->success()
                            ->title('Pedido marcado como pago!')
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->label('Visualizar')
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Order $record): string => static::getUrl('preview', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ])
            ->bulkActions(
                Auth::user()->hasAnyRole(['manager', 'tenant-admin']) 
                    ? [Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ])]
                    : []
            )
            ->headerActions(
                Auth::user()->hasAnyRole(['manager', 'tenant-admin'])
                    ? [
                        ExportAction::make()
                            ->label('Exportar Relatório')
                            ->color(fn (ExportAction $action) => $action->isDisabled() ? 'gray' : 'success')
                            ->icon('heroicon-o-document-arrow-down')
                            ->exporter(OrderExport::class)
                            ->disabled(fn () => Order::query()->count() === 0)
                    ]
                    : []
            
            );
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
            'preview' => Pages\PreviewOrder::route('/{record}/preview'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user->hasRole('tenant-admin')) {
            return $query;
        }

        return $query->where('tenant_id', $user->getTenantId());
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->can('order.list');
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->can('order.edit');
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->can('order.delete');
    }

    public static function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('preview')
                ->label('Visualizar')
                ->icon('heroicon-o-eye')
                ->url(fn (Order $record): string => static::getUrl('preview', ['record' => $record]))
                ->openUrlInNewTab(false),
            Tables\Actions\Action::make('view')
                ->label('Detalhes')
                ->icon('heroicon-o-document-text')
                ->url(fn (Order $record): string => static::getUrl('view', ['record' => $record])),
            Tables\Actions\EditAction::make(),
            Action::make('markAsPaid')
                ->label('Marcar como Pago')
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->payment_status === 'pending')
                ->action(function (Order $record) {
                    $record->markAsPaid();
                    
                    Notification::make()
                        ->success()
                        ->title('Pedido marcado como pago!')
                        ->send();
                }),
        ];
    }
} 