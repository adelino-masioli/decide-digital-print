<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Resources\Pages\Page;
use App\Models\Quote;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuoteMail;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PreviewQuote extends Page
{
    protected static string $resource = QuoteResource::class;

    protected static string $view = 'filament.resources.quote-resource.pages.preview-quote';

    protected static ?string $title = 'Visualizar Orçamento';
    
    protected static ?string $breadcrumb = 'Visualizar';
    
    protected static ?string $pluralModelLabel = 'Orçamentos';

    
    
    public Quote $record;
    
    public $items = [];
    public $subtotal = 0;
    public $discount = 0;
    public $total = 0;
    
    public function mount(Quote $record): void
    {
        $this->record = Quote::with(['client', 'seller', 'items.product'])->findOrFail($record->id);
        
        // Carregar os itens e calcular os valores
        $this->loadItems();
    }
    
    protected function loadItems(): void
    {
        // Garantir que os itens estão carregados
        if (!$this->record->relationLoaded('items')) {
            $this->record->load('items.product');
        }
        
        // Obter os itens e filtrar os inválidos
        $items = $this->record->items;
        
        // Verificar se $items é uma coleção
        if (!($items instanceof Collection)) {
            $items = collect($items);
        }
        
        // Filtrar itens inválidos
        $validItems = collect();
        foreach ($items as $item) {
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
        Log::info('PreviewQuote: Itens carregados', [
            'quote_id' => $this->record->id,
            'items_count' => count($validItems),
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
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => route('filament.admin.resources.quotes.index')),
                
            Action::make('edit')
                ->label('Editar')
                ->color('warning')
                ->icon('heroicon-o-pencil')
                ->url(fn () => route('filament.admin.resources.quotes.edit', ['record' => $this->record])),
                
            Action::make('pdf')
                ->label('Baixar PDF')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Recarregar os itens antes de gerar o PDF
                    $this->loadItems();
                    
                    $pdf = Pdf::loadView('pdf.quote', [
                        'quote' => $this->record,
                        'items' => $this->items,
                        'subtotal' => $this->subtotal,
                        'discount' => $this->discount,
                        'total' => $this->total
                    ]);
                    
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "orcamento_{$this->record->id}.pdf"
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
                    
                    $pdf = Pdf::loadView('pdf.quote', [
                        'quote' => $this->record,
                        'items' => $this->items,
                        'subtotal' => $this->subtotal,
                        'discount' => $this->discount,
                        'total' => $this->total
                    ]);
                    
                    $pdfPath = storage_path("app/public/orcamento_{$this->record->id}.pdf");
                    $pdf->save($pdfPath);

                    Mail::to($data['email'])->send(new QuoteMail($this->record, $pdfPath));

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
        
        return [
            'quote' => $this->record,
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
        ];
    }
} 