<div class="container-login">
    <div class="form-section">
        <div class="form-section-content">
            <h1>{{ $this->getHeading() }}</h1>
            <p>{!! $this->getSubheading() !!}</p>
           

            <form wire:key="register-form" wire:submit.prevent="register">
                <div>
                    {{ $this->form }}
                </div>
                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </form>
            
            <div class="divider">Ou</div>


            <p class="signup-text">Já tem uma conta? <a href="{{ url('admin/login') }}">Acessar</a></p>

            <p class="copyright-text">© {{ date('Y') }} todos os direitos reservados</p>
        </div>
    </div>

    <div class="image-section">
        <img src="{{ asset('images/login-bg1.webp') }}" alt="Login Background">
    </div>
</div>

@push('styles')
    @vite('resources/css/auth.css')
@endpush




{{-- <x-filament-panels::page.simple>
    <x-slot name="subheading">
        {!! $this->getSubheading() !!}
    </x-slot>

    <div class="relative">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </div>

    <div class="mt-4 text-center">
        <p class="text-sm text-gray-600">
            Já tem uma conta?
            <a href="{{ route('filament.admin.auth.login') }}" class="text-primary-600 hover:text-primary-700">
                Faça login
            </a>
        </p>
    </div>
</x-filament-panels::page.simple>  --}}