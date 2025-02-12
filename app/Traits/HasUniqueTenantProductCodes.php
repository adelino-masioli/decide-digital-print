<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasUniqueTenantProductCodes
{
    use HasUniqueTenantSlug;

    /**
     * Gera um SKU único para o produto
     * Formato: CAT-SUB-XXX
     */
    public function generateUniqueSku(Model $product): string
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
        $lastSku = $product->where('tenant_id', $product->tenant_id)
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
        return $skuBase . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Boot do trait
     */
    public static function bootHasUniqueTenantProductCodes()
    {
        static::creating(function ($model) {
            if (!$model->sku) {
                $model->sku = $model->generateUniqueSku($model);
            }
        });
    }
} 