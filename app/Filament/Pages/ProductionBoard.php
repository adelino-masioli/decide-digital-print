<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Filament\Support\Facades\FilamentIcon;
use Livewire\Attributes\Url;
use Filament\Support\Components\ViewComponent;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Actions\StaticAction;
use Filament\Support\Facades\FilamentView;

class ProductionBoard extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Quadro de Produção';
    protected static ?string $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.production-board';
    protected static ?string $title = 'Quadro de Produção';

    #[Url]
    public ?string $search = '';

    public ?Order $selectedOrder = null;

    public function openProductsPanel(int $orderId): void
    {
        $this->selectedOrder = Order::with(['quote.items.product'])->find($orderId);
        $this->mountAction('viewProducts');
    }

    protected function getActions(): array
    {
        return [
            Action::make('viewProducts')
                ->slideOver()
                ->label('Produtos do Pedido')
                ->modalHeading(fn() => "Produtos do Pedido {$this->selectedOrder?->number}")
                ->modalContent(fn() => view('filament.pages.partials.products-modal', [
                    'order' => $this->selectedOrder
                ]))
                ->modalWidth('xl')
                ->hidden(),
        ];
    }

    public function getViewData(): array
    {
        $user = auth()->user();

        $orders = Order::query()
            ->when(!$user->hasRole('super-admin'), function ($query) use ($user) {
                return $query->where('tenant_id', $user->getTenantId());
            })
            ->when($this->search, function ($query) {
                return $query->where(function ($query) {
                    $query->where('number', 'like', "%{$this->search}%")
                        ->orWhereHas('quote.client', function ($query) {
                            $query->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->whereIn('status', [
                Order::STATUS_PROCESSING,
                Order::STATUS_IN_PRODUCTION,
                Order::STATUS_COMPLETED,
            ])
            ->with(['quote.items.product'])
            ->latest('updated_at')
            ->get()
            ->groupBy('status');

        return [
            'columns' => [
                Order::STATUS_PROCESSING => [
                    'title' => 'Em Processamento',
                    'orders' => $orders->get(Order::STATUS_PROCESSING, collect()),
                ],
                Order::STATUS_IN_PRODUCTION => [
                    'title' => 'Em Produção',
                    'orders' => $orders->get(Order::STATUS_IN_PRODUCTION, collect()),
                ],
                Order::STATUS_COMPLETED => [
                    'title' => 'Concluídos',
                    'orders' => $orders->get(Order::STATUS_COMPLETED, collect()),
                ],
            ],
        ];
    }

    #[On('updateOrderStatus')]
    public function updateOrderStatus(int $orderId, string $status): void
    {
        $order = Order::find($orderId);
        
        if (!$order) {
            return;
        }

        $order->update(['status' => $status]);

        Notification::make()
            ->success()
            ->title('Status do pedido atualizado com sucesso!')
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super-admin', 'tenant-admin', 'manager', 'operator']);
    }

    public function getStatusColor(string $status): string
    {
        return match ($status) {
            Order::STATUS_PROCESSING => 'border-amber-500',
            Order::STATUS_IN_PRODUCTION => 'border-blue-500',
            Order::STATUS_COMPLETED => 'border-green-500',
            default => 'border-gray-500',
        };
    }

    public function getStatusIcon(string $status): array
    {
        return match ($status) {
            Order::STATUS_PROCESSING => [
                'icon' => 'heroicon-o-document-text',
                'color' => 'text-amber-500'
            ],
            Order::STATUS_IN_PRODUCTION => [
                'icon' => 'heroicon-o-cog',
                'color' => 'text-blue-500'
            ],
            Order::STATUS_COMPLETED => [
                'icon' => 'heroicon-o-check-circle',
                'color' => 'text-green-500'
            ],
            default => [
                'icon' => 'heroicon-o-document',
                'color' => 'text-gray-500'
            ],
        };
    }

    public function getDropZoneClass(string $status): string
    {
        return match ($status) {
            Order::STATUS_PROCESSING => 'hover:border-amber-500 hover:bg-amber-50 bg-amber-50/30 border-amber-200',
            Order::STATUS_IN_PRODUCTION => 'hover:border-blue-500 hover:bg-blue-50 bg-blue-50/30 border-blue-200',
            Order::STATUS_COMPLETED => 'hover:border-green-500 hover:bg-green-50 bg-green-50/30 border-green-200',
            default => 'hover:border-gray-500 hover:bg-gray-50 bg-gray-50/30 border-gray-200',
        };
    }

    public function getDropZoneText(string $status): string
    {
        return match ($status) {
            Order::STATUS_PROCESSING => 'Mover para Processamento',
            Order::STATUS_IN_PRODUCTION => 'Iniciar Produção',
            Order::STATUS_COMPLETED => 'Marcar como Concluído',
            default => 'Arraste os pedidos para esta área',
        };
    }
} 