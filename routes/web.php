<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterTenantController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('register-tenant', [RegisterTenantController::class, 'create'])
        ->name('register-tenant');
    
    Route::post('register-tenant', [RegisterTenantController::class, 'store']);
});

// Proteger rotas do admin com middleware de tenant
Route::middleware(['auth', 'tenant.access'])->group(function () {
    // Suas rotas protegidas aqui
});
