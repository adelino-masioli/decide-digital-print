<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\KeyValue;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Itens do Pedido';
    protected static ?string $modelLabel = 'Item';
    protected static ?string $pluralModelLabel = 'Itens';

    // Método para formatar as opções de personalização para exibição
    private function formatJsonField($value): string
    {
        // Se for nulo ou booleano, retorna string vazia
        if (is_null($value) || is_bool($value)) {
            return '';
        }
        
        // Se já for uma string, tenta decodificar
        if (is_string($value)) {
            try {
                $decoded = json_decode($value, true);
                // Se a decodificação falhar ou não for um array, retorna o valor original
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                    return $value;
                }
                $value = $decoded;
            } catch (\Exception $e) {
                return $value;
            }
        }
        
        // Se for um array, formata para exibição
        if (is_array($value)) {
            $result = '';
            foreach ($value as $key => $val) {
                $result .= "<strong>{$key}:</strong> ";
                if (is_array($val)) {
                    $result .= implode(', ', $val);
                } else {
                    $result .= $val;
                }
                $result .= "<br>";
            }
            return $result;
        }
        
        // Caso contrário, converte para string
        return (string) $value;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Preço Unitário')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Preço Total')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customization_options')
                    ->label('Opções de Personalização')
                    ->html()
                    ->formatStateUsing(function ($state) {
                        return $this->formatJsonField($state);
                    }),

                Tables\Columns\TextColumn::make('file_requirements')
                    ->label('Requisitos do Arquivo')
                    ->html()
                    ->formatStateUsing(function ($state) {
                        return $this->formatJsonField($state);
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Removido ações pois os itens vêm do orçamento
            ])
            ->bulkActions([
                // Removido ações em massa
            ]);
    }

    protected function getFormComponents(): array
    {
        return [
            KeyValue::make('customization_options')
                ->label('Opções de Personalização')
                ->nullable()
                ->default(null)
                ->keyLabel('Opção')
                ->valueLabel('Valor')
                ->addable()
                ->reorderable()
                ->columnSpan(2)
                ->afterStateHydrated(function ($component, $state) {
                    // Se o estado for uma string JSON, decodifica para array
                    if (is_string($state) && !empty($state)) {
                        try {
                            $decoded = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $component->state($decoded);
                            }
                        } catch (\Exception $e) {
                            // Mantém o estado original se falhar
                        }
                    }
                })
                ->dehydrateStateUsing(function ($state) {
                    // Se o estado for um array, codifica para JSON
                    if (is_array($state) && !empty($state)) {
                        return json_encode($state);
                    }
                    return $state;
                }),

            KeyValue::make('file_requirements')
                ->label('Requisitos do Arquivo')
                ->nullable()
                ->default(null)
                ->keyLabel('Requisito')
                ->valueLabel('Especificação')
                ->addable()
                ->reorderable()
                ->columnSpan(2)
                ->afterStateHydrated(function ($component, $state) {
                    // Se o estado for uma string JSON, decodifica para array
                    if (is_string($state) && !empty($state)) {
                        try {
                            $decoded = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $component->state($decoded);
                            }
                        } catch (\Exception $e) {
                            // Mantém o estado original se falhar
                        }
                    }
                })
                ->dehydrateStateUsing(function ($state) {
                    // Se o estado for um array, codifica para JSON
                    if (is_array($state) && !empty($state)) {
                        return json_encode($state);
                    }
                    return $state;
                }),
        ];
    }
} 