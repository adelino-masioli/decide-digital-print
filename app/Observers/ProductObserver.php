<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductObserver
{
    /**
     * Handle the Product "creating" event.
     */
    public function creating(Product $product): void
    {
        // Gera o slug único
        $this->generateUniqueSlug($product);
        
        // Gera o SKU único
        $this->generateUniqueSku($product);
    }

    /**
     * Handle the Product "updating" event.
     */
    public function updating(Product $product): void
    {
        // Se o nome foi alterado, gera um novo slug
        if ($product->isDirty('name')) {
            $this->generateUniqueSlug($product);
        }
    }

    /**
     * Gera um slug único para o produto
     */
    private function generateUniqueSlug(Product $product): void
    {
        $slug = Str::slug($product->name);
        
        // Verifica se já existe um produto com este slug para o mesmo tenant
        $count = Product::where('tenant_id', $product->tenant_id)
            ->where('slug', 'like', $slug . '%')
            ->when($product->exists, fn($query) => $query->where('id', '!=', $product->id))
            ->count();

        // Se já existir, adiciona um número incremental ao final
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        $product->slug = $slug;
    }

    /**
     * Gera um SKU único para o produto
     * Formato: CAT-SUB-XXX
     * Onde:
     * CAT = 3 primeiras letras da categoria
     * SUB = 3 primeiras letras da subcategoria
     * XXX = número sequencial
     */
    private function generateUniqueSku(Product $product): void
    {
        // Obtém a categoria e subcategoria
        $category = $product->category;
        $subcategory = $product->subcategory;

        // Gera os prefixos da categoria e subcategoria
        $catPrefix = $category ? Str::upper(Str::substr(Str::slug($category->name), 0, 3)) : 'XXX';
        $subPrefix = $subcategory ? Str::upper(Str::substr(Str::slug($subcategory->name), 0, 3)) : 'XXX';

        // Base do SKU
        $skuBase = "{$catPrefix}-{$subPrefix}";

        // Encontra o último número usado para este prefixo
        $lastSku = Product::where('tenant_id', $product->tenant_id)
            ->where('sku', 'like', $skuBase . '-%')
            ->orderBy('sku', 'desc')
            ->value('sku');

        if ($lastSku) {
            // Extrai o número do último SKU e incrementa
            $lastNumber = (int) Str::afterLast($lastSku, '-');
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Formata o número com zeros à esquerda (3 dígitos)
        $product->sku = $skuBase . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
} 