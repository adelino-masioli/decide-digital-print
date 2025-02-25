<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;
use Filament\Notifications\Notification;

class OrderView extends ViewRecord
{
    protected static string $resource = OrderResource::class;
    
    // Desativa o formulário padrão e usa o template personalizado
    protected static string $view = 'filament.resources.order-resource.pages.view-order';
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('pdf')
                ->label('Baixar PDF')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    $pdf = Pdf::loadView('pdf.order', ['order' => $this->record]);
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
                    $pdf = Pdf::loadView('pdf.order', ['order' => $this->record]);
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
    
    /**
     * Prepara os dados para a visualização
     */
    protected function getViewData(): array
    {
        $order = $this->record;
        
        // Carrega os itens do pedido com eager loading do produto
        $items = $order->items()->with('product')->get();
        
        // Calcula os valores financeiros
        $subtotal = 0;
        $discount = 0;
        
        foreach ($items as $item) {
            $subtotal += $item->quantity * $item->unit_price;
            $discount += $item->discount_amount;
        }
        
        $total = $subtotal - $discount;
        
        return [
            'order' => $order,
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
        ];
    }
} 