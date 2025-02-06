<div class="container-login">
    <div class="form-section">
        <div class="form-section-content">
            <h1>{{ $this->getHeading() }}</h1>
            <p>{!! $this->getSubheading() !!}</p>
            <form wire:key="login-form" wire:submit.prevent="authenticate">
                <div>
                    {{ $this->form }}
                </div>

                <x-filament-panels::form.actions 
                    :actions="$this->getCachedFormActions()"
                    :full-width="true"
                />
            </form>
            <div class="divider">Or</div>


            <p class="signup-text">Você não tem uma conta? <a href="#">Cadastre-se</a></p>

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