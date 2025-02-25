<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;
use Filament\Notifications\Notification;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PreviewOrder extends Page
{
    protected static string $resource = OrderResource::class;
    
    protected static string $view = 'filament.resources.order-resource.pages.preview-order';
    
    protected static ?string $title = 'Visualizar Pedido';
    
    protected static ?string $breadcrumb = 'Visualizar';
    
    public Order $record;
    
    public $items = [];
    public $subtotal = 0;
    public $discount = 0;
    public $total = 0;
    
    public function mount(Order $record): void
    {
        // Carregar o pedido com todos os relacionamentos necessários
        $this->record = Order::with([
            'quote.client',
            'quote.seller',
            'items.product',
        ])->findOrFail($record->id);
        
        // Carregar os itens e calcular os valores
        $this->loadItems();
        
        // Log para debug
        Log::info('PreviewOrder: Montagem concluída', [
            'order_id' => $this->record->id,
            'has_items_relation' => $this->record->relationLoaded('items'),
            'items_count' => is_array($this->record->items) || $this->record->items instanceof Collection ? count($this->record->items) : 'não contável',
        ]);
    }
    
    protected function loadItems(): void
    {
        // Verificar se o relacionamento de itens está carregado
        if (!$this->record->relationLoaded('items')) {
            Log::warning('PreviewOrder: Relacionamento de itens não carregado, carregando agora...');
            $this->record->load(['items.product']);
        }
        
        // Obter os itens
        $items = $this->record->items;
        
        // Verificar se $items é uma coleção ou um único item
        if ($items instanceof Collection) {
            $itemsCollection = $items;
        } elseif (is_array($items)) {
            $itemsCollection = collect($items);
        } else {
            // Se for um único item, crie uma coleção com ele
            $itemsCollection = collect([$items]);
            Log::warning('PreviewOrder: items não é uma coleção, mas um único objeto', [
                'type' => get_class($items),
            ]);
        }
        
        // Filtrar itens inválidos
        $validItems = collect();
        foreach ($itemsCollection as $item) {
            if (is_object($item) && !is_bool($item)) {
                $validItems->push($item);
            }
        }
        
        // Calcular valores financeiros
        $subtotal = 0;
        $discount = 0;
        
        foreach ($validItems as $item) {
            $quantity = isset($item->quantity) && is_numeric($item->quantity) ? $item->quantity : 0;
            $unitPrice = isset($item->unit_price) && is_numeric($item->unit_price) ? $item->unit_price : 0;
            $discountAmount = isset($item->discount_amount) && is_numeric($item->discount_amount) ? $item->discount_amount : 0;
            
            $subtotal += $quantity * $unitPrice;
            $discount += $discountAmount;
        }
        
        $total = $subtotal - $discount;
        
        // Atribuir aos atributos da classe
        $this->items = $validItems;
        $this->subtotal = $subtotal;
        $this->discount = $discount;
        $this->total = $total;
        
        // Log para debug
        Log::info('PreviewOrder: Itens carregados', [
            'order_id' => $this->record->id,
            'items_count' => $validItems->count(),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total
        ]);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Voltar')
                ->url(fn () => OrderResource::getUrl())
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
                
            Action::make('edit')
                ->label('Editar')
                ->url(fn () => OrderResource::getUrl('edit', ['record' => $this->record]))
                ->color('warning')
                ->icon('heroicon-o-pencil'),
                
            Action::make('pdf')
                ->label('Baixar PDF')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Recarregar os itens antes de gerar o PDF
                    $this->loadItems();
                    
                    $pdf = Pdf::loadView('pdf.order', [
                        'order' => $this->record,
                        'items' => $this->items,
                        'subtotal' => $this->subtotal,
                        'discount' => $this->discount,
                        'total' => $this->total
                    ]);
                    
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "pedido_{$this->record->id}.pdf"
                    );
                }),
                
            Action::make('email')
                ->label('Enviar por Email')
                ->color('primary')
                ->icon('heroicon-o-envelope')
                ->form([
                    \Filament\Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Recarregar os itens antes de gerar o PDF
                    $this->loadItems();
                    
                    $pdf = Pdf::loadView('pdf.order', [
                        'order' => $this->record,
                        'items' => $this->items,
                        'subtotal' => $this->subtotal,
                        'discount' => $this->discount,
                        'total' => $this->total
                    ]);
                    
                    $pdfPath = storage_path("app/public/pedido_{$this->record->id}.pdf");
                    $pdf->save($pdfPath);

                    Mail::to($data['email'])->send(new OrderMail($this->record, $pdfPath));

                    unlink($pdfPath);

                    Notification::make()
                        ->title('Email enviado com sucesso!')
                        ->success()
                        ->send();
                }),
        ];
    }
    
    protected function getViewData(): array
    {
        // Garantir que os itens estão carregados
        if (empty($this->items)) {
            $this->loadItems();
        }
        
        // Log para debug
        Log::info('PreviewOrder: getViewData chamado', [
            'items_count' => $this->items instanceof Collection ? $this->items->count() : 'não contável',
        ]);
        
        return [
            'order' => $this->record,
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
        ];
    }
} 