<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Opportunity;

class OpportunityTotalValue extends Component
{
    public $opportunityId;
    
    protected $listeners = [
        'items-changed' => '$refresh',
        'opportunity-items-updated' => '$refresh'
    ];

    public function mount($opportunityId = null)
    {
        $this->opportunityId = $opportunityId;
    }

    public function render()
    {
        $total = 'R$ 0,00';
        
        if ($this->opportunityId) {
            // Forçar busca fresca do banco de dados
            $opportunity = Opportunity::query()
                ->withSum('items', 'total_price')
                ->find($this->opportunityId);
                
            if ($opportunity) {
                $total = 'R$ ' . number_format($opportunity->calculateTotal(), 2, ',', '.');
            }
        }

        return view('livewire.opportunity-total-value', [
            'total' => $total
        ]);
    }
} 