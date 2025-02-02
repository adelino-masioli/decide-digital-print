<div>
    <div class="w-full h-screen">
        <div class="flex w-full h-full">
            {{-- Lado esquerdo (formulário) --}}
            <div class="flex flex-col items-center justify-center w-1/2">
               <div class="flex flex-col items-start justify-center w-1/2">
                <h2 class="mb-4 text-2xl font-bold text-left text-gray-900 dark:text-white">Decide Digital - Print</h2>

                <form wire:submit.prevent="authenticate" class="w-full max-w-sm">
                   <div class="flex flex-col pb-4">
                    {{ $this->form }}
                   </div>

                    
                    <x-filament-panels::form.actions 
                            :actions="$this->getCachedFormActions()"
                            :full-width="true"
                        />
                </form>
               </div>
            </div>

            {{-- Lado direito (imagem) --}}
            <div class="w-1/2 h-full">
                <img src="{{ asset('images/login-image.webp') }}" class="object-cover w-full h-full">
            </div>
        </div>
    </div>
</div>
