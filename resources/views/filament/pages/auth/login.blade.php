<div class="container-login">
    <!-- Seção do formulário -->
    <div class="form-section">
        <div class="form-section-content">
            <h1>Decide Digital - Print</h1>
            <form wire:key="login-form" wire:submit.prevent="authenticate">
                <div>
                    {{ $this->form }}
                </div>

                <x-filament-panels::form.actions 
                    :actions="$this->getCachedFormActions()"
                    :full-width="true"
                />
            </form>
        </div>
    </div>

    <!-- Seção da imagem -->
    <div class="image-section">
        <img src="{{ asset('images/login-bg1.webp') }}" alt="Login Background">
    </div>
</div>


@push('styles')
    @vite('resources/css/auth.css')
@endpush