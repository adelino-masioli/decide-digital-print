<x-guest-layout>
    {{-- <div class="w-full max-w-4xl mx-auto">
        <div class="overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <!-- Header -->
            <div class="p-6 bg-gradient-to-r from-indigo-500 to-purple-600">
                <h2 class="text-2xl font-bold text-center text-white">Cadastro de Gráfica</h2>
                <p class="mt-2 text-center text-indigo-100">Preencha o formulário abaixo para começar</p>
            </div>

            <form method="POST" action="{{ route('register-tenant') }}" class="p-6" id="registrationForm">
                @csrf
                
                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="relative flex items-center justify-between">
                        <div class="absolute w-full transform -translate-y-1/2 top-1/2">
                            <div class="h-1 bg-gray-200">
                                <div class="h-1 transition-all duration-300 bg-indigo-500" style="width: 0%" id="progress-bar"></div>
                            </div>
                        </div>
                        <button type="button" class="relative z-10 flex items-center justify-center w-10 h-10 font-semibold text-white bg-indigo-500 rounded-full step-button active" data-step="1">
                            1
                        </button>
                        <button type="button" class="relative z-10 flex items-center justify-center w-10 h-10 font-semibold text-gray-600 bg-gray-200 rounded-full step-button" data-step="2">
                            2
                        </button>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-sm font-medium text-indigo-600">Informações Básicas</span>
                        <span class="text-sm font-medium text-gray-500">Informações Adicionais</span>
                    </div>
                </div>

                <!-- Step 1: Basic Information -->
                <div class="space-y-6 step-content" id="step1">
                    <div class="p-6 rounded-lg bg-gray-50 dark:bg-gray-900">
                        <h3 class="mb-6 text-lg font-medium text-gray-900 dark:text-gray-100">Dados Pessoais</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Nome')" />
                                <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" class="mt-1" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Last Name -->
                            <div>
                                <x-input-label for="last_name" :value="__('Sobrenome')" />
                                <x-text-input id="last_name" type="text" name="last_name" :value="old('last_name')" required class="mt-1" />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" class="mt-1" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-6 mt-6">
                            <div>
                                <x-input-label for="document" :value="__('CPF/CNPJ')" />
                                <x-text-input id="document" type="text" name="document" :value="old('document')" required class="mt-1" />
                                <x-input-error :messages="$errors->get('document')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="__('Telefone')" />
                                <x-text-input id="phone" type="text" name="phone" :value="old('phone')" required class="mt-1" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mt-6">
                            <div>
                                <x-input-label for="password" :value="__('Senha')" />
                                <x-text-input id="password" type="password" name="password" required autocomplete="new-password" class="mt-1" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />
                                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required class="mt-1" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Additional Information -->
                <div class="hidden space-y-6 step-content" id="step2">
                    <!-- Company Information -->
                    <div class="p-6 rounded-lg bg-gray-50 dark:bg-gray-900">
                        <h3 class="mb-6 text-lg font-medium text-gray-900 dark:text-gray-100">Dados da Empresa</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="company_name" :value="__('Nome da Empresa')" />
                                <x-text-input id="company_name" type="text" name="company_name" :value="old('company_name')" required class="mt-1" />
                                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="trading_name" :value="__('Nome Fantasia')" />
                                <x-text-input id="trading_name" type="text" name="trading_name" :value="old('trading_name')" class="mt-1" />
                                <x-input-error :messages="$errors->get('trading_name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mt-6">
                            <div>
                                <x-input-label for="state_registration" :value="__('Inscrição Estadual')" />
                                <x-text-input id="state_registration" type="text" name="state_registration" :value="old('state_registration')" class="mt-1" />
                                <x-input-error :messages="$errors->get('state_registration')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="municipal_registration" :value="__('Inscrição Municipal')" />
                                <x-text-input id="municipal_registration" type="text" name="municipal_registration" :value="old('municipal_registration')" class="mt-1" />
                                <x-input-error :messages="$errors->get('municipal_registration')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label for="company_address" :value="__('Endereço da Empresa')" />
                            <x-text-input id="company_address" type="text" name="company_address" :value="old('company_address')" required class="mt-1" />
                            <x-input-error :messages="$errors->get('company_address')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-6 mt-6">
                            <div>
                                <x-input-label for="company_latitude" :value="__('Latitude')" />
                                <x-text-input id="company_latitude" type="number" step="any" name="company_latitude" :value="old('company_latitude')" class="mt-1" />
                                <x-input-error :messages="$errors->get('company_latitude')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="company_longitude" :value="__('Longitude')" />
                                <x-text-input id="company_longitude" type="number" step="any" name="company_longitude" :value="old('company_longitude')" class="mt-1" />
                                <x-input-error :messages="$errors->get('company_longitude')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="p-6 rounded-lg bg-gray-50 dark:bg-gray-900">
                        <h3 class="mb-6 text-lg font-medium text-gray-900 dark:text-gray-100">Endereço de Correspondência</h3>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="zip_code" :value="__('CEP')" />
                                <x-text-input id="zip_code" type="text" name="zip_code" :value="old('zip_code')" required class="mt-1" />
                                <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="street" :value="__('Rua')" />
                                <x-text-input id="street" type="text" name="street" :value="old('street')" required class="mt-1" />
                                <x-input-error :messages="$errors->get('street')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-6 mt-6">
                            <div>
                                <x-input-label for="number" :value="__('Número')" />
                                <x-text-input id="number" type="text" name="number" :value="old('number')" required class="mt-1" />
                                <x-input-error :messages="$errors->get('number')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="complement" :value="__('Complemento')" />
                                <x-text-input id="complement" type="text" name="complement" :value="old('complement')" class="mt-1" />
                                <x-input-error :messages="$errors->get('complement')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="neighborhood" :value="__('Bairro')" />
                                <x-text-input id="neighborhood" type="text" name="neighborhood" :value="old('neighborhood')" required class="mt-1" />
                                <x-input-error :messages="$errors->get('neighborhood')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mt-6">
                            <div>
                                <x-input-label for="state_id" :value="__('Estado')" />
                                <select id="state_id" name="state_id" class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="">Selecione um estado</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('state_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="city_id" :value="__('Cidade')" />
                                <select id="city_id" name="city_id" class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="">Selecione um estado primeiro</option>
                                </select>
                                <x-input-error :messages="$errors->get('city_id')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 mt-8 border-t border-gray-200">
                    <a class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white" href="">
                        {{ __('Já tem cadastro?') }}
                    </a>

                    <div class="flex items-center space-x-4">
                        <x-button type="button" id="prevBtn" class="hidden bg-gray-600 hover:bg-gray-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            {{ __('Anterior') }}
                        </x-button>
                        
                        <x-button type="button" id="nextBtn">
                            {{ __('Próximo') }}
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </x-button>

                        <x-button type="submit" id="submitBtn" class="hidden bg-green-600 hover:bg-green-700">
                            {{ __('Registrar') }}
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </div> --}}


    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <h2 class="header-title">Cadastro de Gráfica</h2>
                <p class="header-subtitle">Preencha o formulário abaixo para começar</p>
            </div>
    
            <form method="POST" action="{{ route('register-tenant') }}" class="form" id="registrationForm">
                @csrf
                
                <!-- Progress Steps -->
                <div class="progress-container">
                    <div class="progress-wrapper">
                        <div class="progress-line">
                            <div class="progress-line-fill" style="width: 0%" id="progress-bar"></div>
                        </div>
                        <button type="button" class="progress-step active" data-step="1">1</button>
                        <button type="button" class="progress-step" data-step="2">2</button>
                    </div>
                    <div class="progress-labels">
                        <span class="progress-label active">Informações Básicas</span>
                        <span class="progress-label">Informações Adicionais</span>
                    </div>
                </div>
    
                <!-- Step 1: Basic Information -->
                <div class="step-content" id="step1">
                    <div class="panel">
                        <h3 class="panel-title">Dados Pessoais</h3>
                        <div class="form-grid-2">
                            <!-- Name -->
                            <div class="form-group">
                                <x-input-label for="name" :value="__('Nome')" />
                                <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>
    
                            <!-- Last Name -->
                            <div class="form-group">
                                <x-input-label for="last_name" :value="__('Sobrenome')" />
                                <x-text-input id="last_name" type="text" name="last_name" :value="old('last_name')" required />
                                <x-input-error :messages="$errors->get('last_name')" />
                            </div>
                        </div>
    
                        <div class="form-group">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" />
                        </div>
    
                        <div class="form-grid-2">
                            <div class="form-group">
                                <x-input-label for="document" :value="__('CPF/CNPJ')" />
                                <x-text-input id="document" type="text" name="document" :value="old('document')" required />
                                <x-input-error :messages="$errors->get('document')" />
                            </div>
    
                            <div class="form-group">
                                <x-input-label for="phone" :value="__('Telefone')" />
                                <x-text-input id="phone" type="text" name="phone" :value="old('phone')" required />
                                <x-input-error :messages="$errors->get('phone')" />
                            </div>
                        </div>
    
                        <div class="form-grid-2">
                            <div class="form-group">
                                <x-input-label for="password" :value="__('Senha')" />
                                <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" />
                            </div>
    
                            <div class="form-group">
                                <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />
                                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" />
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Step 2: Additional Information -->
                <div class="hidden step-content" id="step2">
                    <!-- Company Information -->
                    <div class="panel">
                        <h3 class="panel-title">Dados da Empresa</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <x-input-label for="company_name" :value="__('Nome da Empresa')" />
                                <x-text-input id="company_name" type="text" name="company_name" :value="old('company_name')" required />
                                <x-input-error :messages="$errors->get('company_name')" />
                            </div>
    
                            <div class="form-group">
                                <x-input-label for="trading_name" :value="__('Nome Fantasia')" />
                                <x-text-input id="trading_name" type="text" name="trading_name" :value="old('trading_name')" />
                                <x-input-error :messages="$errors->get('trading_name')" />
                            </div>
                        </div>
    
                        <div class="form-grid-2">
                            <div class="form-group">
                                <x-input-label for="state_registration" :value="__('Inscrição Estadual')" />
                                <x-text-input id="state_registration" type="text" name="state_registration" :value="old('state_registration')" />
                                <x-input-error :messages="$errors->get('state_registration')" />
                            </div>
    
                            <div class="form-group">
                                <x-input-label for="municipal_registration" :value="__('Inscrição Municipal')" />
                                <x-text-input id="municipal_registration" type="text" name="municipal_registration" :value="old('municipal_registration')" />
                                <x-input-error :messages="$errors->get('municipal_registration')" />
                            </div>
                        </div>
    
                        <div class="form-group">
                            <x-input-label for="company_address" :value="__('Endereço da Empresa')" />
                            <x-text-input id="company_address" type="text" name="company_address" :value="old('company_address')" required />
                            <x-input-error :messages="$errors->get('company_address')" />
                        </div>
    
                        <div class="form-grid-2">
                            <div class="form-group">
                                <x-input-label for="company_latitude" :value="__('Latitude')" />
                                <x-text-input id="company_latitude" type="number" step="any" name="company_latitude" :value="old('company_latitude')" />
                                <x-input-error :messages="$errors->get('company_latitude')" />
                            </div>
    
                            <div class="form-group">
                                <x-input-label for="company_longitude" :value="__('Longitude')" />
                                <x-text-input id="company_longitude" type="number" step="any" name="company_longitude" :value="old('company_longitude')" />
                                <x-input-error :messages="$errors->get('company_longitude')" />
                            </div>
                        </div>
                    </div>
    
                    <!-- Address Information -->
                    <div class="panel">
                        <h3 class="panel-title">Endereço de Correspondência</h3>
                        
                        <div class="form-grid-2">
                            <div class="form-group">
                                <x-input-label for="zip_code" :value="__('CEP')" />
                                <x-text-input id="zip_code" type="text" name="zip_code" :value="old('zip_code')" required />
                                <x-input-error :messages="$errors->get('zip_code')" />
                            </div>
    
                            <div class="form-group">
                                <x-input-label for="street" :value="__('Rua')" />
                                <x-text-input id="street" type="text" name="street" :value="old('street')" required />
                                <x-input-error :messages="$errors->get('street')" />
                            </div>
                        </div>
    
                        <div class="form-grid-3">
                            <div class="form-group">
                                <x-input-label for="number" :value="__('Número')" />
                                <x-text-input id="number" type="text" name="number" :value="old('number')" required />
                                <x-input-error :messages="$errors->get('number')" />
                            </div>
    
                            <div class="form-group">
                                <x-input-label for="complement" :value="__('Complemento')" />
                                <x-text-input id="complement" type="text" name="complement" :value="old('complement')" />
                                <x-input-error :messages="$errors->get('complement')" />
                            </div>
    
                            <div class="form-group">
                                <x-input-label for="neighborhood" :value="__('Bairro')" />
                                <x-text-input id="neighborhood" type="text" name="neighborhood" :value="old('neighborhood')" required />
                                <x-input-error :messages="$errors->get('neighborhood')" />
                            </div>
                        </div>
    
                        <div class="form-grid-2">
                            <div class="form-group">
                                <x-input-label for="state_id" :value="__('Estado')" />
                                <select id="state_id" name="state_id" class="select-field" required>
                                    <option value="">Selecione um estado</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('state_id')" />
                            </div>
    
                            <div class="form-group">
                                <x-input-label for="city_id" :value="__('Cidade')" />
                                <select id="city_id" name="city_id" class="select-field" required>
                                    <option value="">Selecione um estado primeiro</option>
                                </select>
                                <x-input-error :messages="$errors->get('city_id')" />
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="form-footer">
                    <a class="login-link" href="">
                        {{ __('Já tem cadastro?') }}
                    </a>
    
                    <div class="button-group">
                        <button type="button" id="prevBtn" class="hidden button button-secondary">
                            <svg class="button-icon" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            {{ __('Anterior') }}
                        </button>
                        
                        <button type="button" id="nextBtn" class="button button-primary">
                            {{ __('Próximo') }}
                            <svg class="button-icon" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
    
                        <button type="submit" id="submitBtn" class="hidden button button-success">
                            {{ __('Registrar') }}
                            <svg class="button-icon" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const form = document.getElementById('registrationForm');
            const nextBtn = document.getElementById('nextBtn');
            const prevBtn = document.getElementById('prevBtn');
            const submitBtn = document.getElementById('submitBtn');
            const stepButtons = document.querySelectorAll('.step-button');
            const stateSelect = document.getElementById('state_id');
            const citySelect = document.getElementById('city_id');
            const progressBar = document.getElementById('progress-bar');

            function updateProgressBar(step) {
                progressBar.style.width = step === 1 ? '0%' : '100%';
            }

            // Handle step navigation
            function showStep(step) {
                document.querySelectorAll('.step-content').forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(`step${step}`).classList.remove('hidden');
                
                stepButtons.forEach(button => {
                    const buttonStep = parseInt(button.dataset.step);
                    button.classList.remove('bg-indigo-500', 'text-white', 'bg-gray-200', 'text-gray-600');
                    if (buttonStep === step) {
                        button.classList.add('bg-indigo-500', 'text-white');
                    } else if (buttonStep < step) {
                        button.classList.add('bg-indigo-500', 'text-white');
                    } else {
                        button.classList.add('bg-gray-200', 'text-gray-600');
                    }
                });

                updateProgressBar(step);

                // Update buttons
                if (step === 1) {
                    prevBtn.classList.add('hidden');
                    nextBtn.classList.remove('hidden');
                    submitBtn.classList.add('hidden');
                } else {
                    prevBtn.classList.remove('hidden');
                    nextBtn.classList.add('hidden');
                    submitBtn.classList.remove('hidden');
                }
            }

            // Step button clicks
            stepButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const step = parseInt(button.dataset.step);
                    if (step === 2 && !validateStep1()) return;
                    currentStep = step;
                    showStep(currentStep);
                });
            });

            // Next button click
            nextBtn.addEventListener('click', () => {
                if (validateStep1()) {
                    currentStep = 2;
                    showStep(currentStep);
                }
            });

            // Previous button click
            prevBtn.addEventListener('click', () => {
                currentStep = 1;
                showStep(currentStep);
            });

            // Validate step 1
            function validateStep1() {
                const requiredFields = ['name', 'last_name', 'email', 'document', 'phone', 'password', 'password_confirmation'];
                let isValid = true;

                requiredFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value) {
                        input.classList.add('border-red-500');
                        isValid = false;
                    } else {
                        input.classList.remove('border-red-500');
                    }
                });

                if (!isValid) {
                    alert('Por favor, preencha todos os campos obrigatórios.');
                }

                return isValid;
            }

            // Handle state/city selection
            stateSelect.addEventListener('change', async function() {
                const stateId = this.value;
                if (!stateId) {
                    citySelect.innerHTML = '<option value="">Selecione um estado primeiro</option>';
                    return;
                }

                try {
                    const response = await fetch(`/api/states/${stateId}/cities`);
                    const cities = await response.json();
                    
                    citySelect.innerHTML = '<option value="">Selecione uma cidade</option>';
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        if (city.id === {{ old('city_id') ?? 'null' }}) {
                            option.selected = true;
                        }
                        citySelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error fetching cities:', error);
                }
            });

            // Trigger initial state selection if there's an old value
            if (stateSelect.value) {
                stateSelect.dispatchEvent(new Event('change'));
            }

            // Format document input (CPF/CNPJ)
            const documentInput = document.getElementById('document');
            documentInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                } else {
                    value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
                }
                e.target.value = value;
            });

            // Format phone input
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 10) {
                    value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                } else {
                    value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                }
                e.target.value = value;
            });

            // Format CEP input
            const cepInput = document.getElementById('zip_code');
            cepInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
                e.target.value = value;
            });
        });
    </script>
    @endpush
</x-guest-layout> 