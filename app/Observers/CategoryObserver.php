<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
    /**
     * Handle the Category "creating" event.
     */
    public function creating(Category $category): void
    {
        // Gera o slug base
        $slug = Str::slug($category->name);
        
        // Verifica se já existe uma categoria com este slug para o mesmo tenant
        $count = Category::where('tenant_id', $category->tenant_id)
            ->where('slug', 'like', $slug . '%')
            ->count();

        // Se já existir, adiciona um número incremental ao final
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        $category->slug = $slug;
    }

    /**
     * Handle the Category "updating" event.
     */
    public function updating(Category $category): void
    {
        // Se o nome foi alterado, gera um novo slug
        if ($category->isDirty('name')) {
            $slug = Str::slug($category->name);
            
            // Verifica se já existe uma categoria com este slug para o mesmo tenant
            // excluindo a categoria atual
            $count = Category::where('tenant_id', $category->tenant_id)
                ->where('slug', 'like', $slug . '%')
                ->where('id', '!=', $category->id)
                ->count();

            // Se já existir, adiciona um número incremental ao final
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }

            $category->slug = $slug;
        }
    }
} 