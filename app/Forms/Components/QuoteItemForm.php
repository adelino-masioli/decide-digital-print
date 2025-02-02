<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\RawJs;

class QuoteItemForm extends Component
{
    public static function make(): Grid
    {
        $user = auth()->user();

        return Grid::make()
            ->schema([
                Select::make('product_id')
                    ->label('Produto')
                    ->relationship(
                        'product',
                        'name',
                        fn (Builder $query) => $user->hasRole('super-admin') 
                            ? $query 
                            : $query->where('tenant_id', $user->getTenantId())
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $product = Product::find($state);
                            $set('unit_price', $product->base_price);
                            $set('total_price', $product->base_price);
                            $set('customization_options', $product->customization_options);
                            $set('file_requirements', $product->file_requirements);
                        }
                    }),

                TextInput::make('quantity')
                    ->label('Quantidade')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->step(1)
                    ->required()
                    ->live()
                    ->disabled(fn ($get) => !$get('product_id'))
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $unit_price_raw = $get('unit_price');
                        
                        // Trata o valor do unit_price da mesma forma que em outros lugares
                        if (!$unit_price_raw) {
                            $unit_price = 0;
                        } else {
                            $unit_price_clean = preg_replace('/[^\d,.]/', '', $unit_price_raw);
                            
                            if (str_contains($unit_price_clean, '.') && !str_contains($unit_price_clean, ',')) {
                                $unit_price = (float) $unit_price_clean;
                            } else {
                                $withoutThousands = str_replace('.', '', $unit_price_clean);
                                $withDotDecimal = str_replace(',', '.', $withoutThousands);
                                $unit_price = (float) $withDotDecimal;
                            }
                        }
                        
                        $quantity = max(1, (int) $state);
                        $total = $unit_price * $quantity;
                        $set('total_price', number_format($total, 2, ',', '.'));
                    }),

                TextInput::make('unit_price')
                    ->label('Preço Unitário')
                    ->required()
                    ->prefix('R$')
                    ->inputMode('decimal')
                    ->step('0.01')
                    ->live()
                    ->mask(RawJs::make(<<<'JS'
                        $money($input, ',', '.', 2)
                    JS))
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2, ',', '.') : null)
                    ->dehydrateStateUsing(function ($state) {
                        if (!$state) return null;
                        
                        // Remove qualquer caractere que não seja número, vírgula ou ponto
                        $state = preg_replace('/[^\d,.]/', '', $state);
                        
                        // Se o valor já está no formato americano (com ponto), retorna direto
                        if (str_contains($state, '.') && !str_contains($state, ',')) {
                            return (float) $state;
                        }
                        
                        // Se estiver no formato brasileiro, converte
                        $withoutThousands = str_replace('.', '', $state);
                        $withDotDecimal = str_replace(',', '.', $withoutThousands);
                        return (float) $withDotDecimal;
                    })
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if ($state) {
                            $state = preg_replace('/[^\d,.]/', '', $state);
                            
                            if (str_contains($state, '.') && !str_contains($state, ',')) {
                                $unit_price = (float) $state;
                            } else {
                                $withoutThousands = str_replace('.', '', $state);
                                $withDotDecimal = str_replace(',', '.', $withoutThousands);
                                $unit_price = (float) $withDotDecimal;
                            }
                            
                            $quantity = (float) $get('quantity');
                            $total = $unit_price * $quantity;
                            $set('total_price', number_format($total, 2, ',', '.'));
                        }
                    }),

                TextInput::make('total_price')
                    ->label('Preço Total')
                    ->prefix('R$')
                    ->inputMode('decimal')
                    ->step('0.01')
                    ->disabled()
                    ->dehydrated()
                    ->mask(RawJs::make(<<<'JS'
                        $money($input, ',', '.', 2)
                    JS))
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2, ',', '.') : null)
                    ->dehydrateStateUsing(function ($state) {
                        if (!$state) return null;
                        
                        // Remove qualquer caractere que não seja número, vírgula ou ponto
                        $state = preg_replace('/[^\d,.]/', '', $state);
                        
                        if (str_contains($state, '.') && !str_contains($state, ',')) {
                            return (float) $state;
                        }
                        
                        $withoutThousands = str_replace('.', '', $state);
                        $withDotDecimal = str_replace(',', '.', $withoutThousands);
                        return (float) $withDotDecimal;
                    }),

                KeyValue::make('customization_options')
                    ->label('Opções de Personalização')
                    ->keyLabel('Opção')
                    ->valueLabel('Valor')
                    ->reorderable(),

                KeyValue::make('file_requirements')
                    ->label('Requisitos do Arquivo')
                    ->keyLabel('Requisito')
                    ->valueLabel('Especificação')
                    ->reorderable(),
            ]);
    }
} 