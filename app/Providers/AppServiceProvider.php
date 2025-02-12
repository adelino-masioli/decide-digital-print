<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

use Livewire\Livewire;
use App\Livewire\WelcomeModal;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Observers\UserObserver;
use App\Observers\CategoryObserver;
use App\Observers\ProductObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::componentNamespace('App\\View\\Components', 'app');

        // Registra o layout auth como um componente anônimo
        Blade::component('layouts.auth', 'layouts.auth');

        // Register Blade Components
        Blade::component('layouts.guest', 'guest-layout');

        Livewire::component('welcome-modal', WelcomeModal::class);

        // Register Observers
        User::observe(UserObserver::class);
        Category::observe(CategoryObserver::class);
        Product::observe(ProductObserver::class);
    }
}
