<?php

namespace App\Observers;

use App\Models\User;
use Database\Seeders\TenantSeeder;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Log::info('UserObserver created event triggered', [
            'user_id' => $user->id,
            'is_tenant_admin' => $user->is_tenant_admin,
            'tenant_id' => $user->tenant_id
        ]);

        // Se for um tenant-admin, atualizar tenant_id e criar dados iniciais
        if ($user->is_tenant_admin && !$user->tenant_id) {
            Log::info('Updating tenant_id for tenant admin', ['user_id' => $user->id]);
            
            try {
                // Atualiza o tenant_id
                $user->tenant_id = $user->id;
                $user->save();

                Log::info('Creating initial data for tenant', ['tenant_id' => $user->id]);
                
                // Cria os dados iniciais
                $seeder = new TenantSeeder();
                $seeder->run($user->id);
                
                Log::info('Initial data created successfully');
            } catch (\Exception $e) {
                Log::error('Error in tenant setup', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }
} 