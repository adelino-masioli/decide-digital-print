<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        @if ($showModal)
            <livewire:welcome-modal />
        @endif
        
        {{-- Debug information --}}
        @if (app()->environment('local'))
            <div class="hidden">
                Debug: Modal should show? {{ $showModal ? 'Yes' : 'No' }}
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
