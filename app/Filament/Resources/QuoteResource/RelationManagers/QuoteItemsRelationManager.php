<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class QuoteItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Itens do Orçamento';

    public function form(Form $form): Form
    {
        $user = Auth::user();

        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->label('Produto')
                ->relationship(
                    'product',
                    'name',
                    fn (Builder $query) => $query->where('tenant_id', $user->tenant_id)
                )
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $product = Product::find($state);
                        if ($product) {
                            $set('unit_price', number_format($product->base_price, 2, ',', '.'));
                            $set('total_price', number_format($product->base_price, 2, ',', '.'));
                            
                            // Carrega as opções de personalização do produto
                            if (!empty($product->customization_options)) {
                                try {
                                    if (is_string($product->customization_options)) {
                                        $customizationOptions = json_decode($product->customization_options, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($customizationOptions)) {
                                            $set('customization_options', $customizationOptions);
                                        }
                                    } else {
                                        $set('customization_options', $product->customization_options);
                                    }
                                } catch (\Exception $e) {
                                    // Em caso de erro, deixa em branco
                                    $set('customization_options', []);
                                }
                            } else {
                                $set('customization_options', []);
                            }
                            
                            // Carrega os requisitos de arquivo do produto
                            if (!empty($product->file_requirements)) {
                                try {
                                    if (is_string($product->file_requirements)) {
                                        $fileRequirements = json_decode($product->file_requirements, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($fileRequirements)) {
                                            $set('file_requirements', $fileRequirements);
                                        }
                                    } else {
                                        $set('file_requirements', $product->file_requirements);
                                    }
                                } catch (\Exception $e) {
                                    // Em caso de erro, deixa em branco
                                    $set('file_requirements', []);
                                }
                            } else {
                                $set('file_requirements', []);
                            }
                        }
                    }
                }),

            Forms\Components\TextInput::make('quantity')
                ->label('Quantidade')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->step(1)
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $unit_price = (float) str_replace(['.', ','], ['', '.'], $get('unit_price'));
                    $total = $state * $unit_price;
                    $set('total_price', number_format($total, 2, ',', '.'));
                }),

            Forms\Components\TextInput::make('unit_price')
                ->label('Preço Unitário')
                ->required()
                ->reactive()
                ->prefix('R$')
                ->afterStateHydrated(function ($component, $state) {
                    if (is_numeric($state)) {
                        $component->state(number_format((float)$state, 2, ',', '.'));
                    }
                })
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $cleanState = preg_replace('/[^\d,]/', '', $state);
                    $cleanState = str_replace(',', '.', $cleanState);
                    $unit_price = (float) $cleanState;
                    
                    $quantity = (int) $get('quantity');
                    $total = $quantity * $unit_price;
                    
                    $set('total_price', number_format($total, 2, ',', '.'));
                })
                ->dehydrateStateUsing(function ($state) {
                    $cleanState = preg_replace('/[^\d,]/', '', $state);
                    return (float) str_replace(',', '.', $cleanState);
                }),

            Forms\Components\TextInput::make('total_price')
                ->label('Preço Total')
                ->disabled()
                ->dehydrated()
                ->prefix('R$')
                ->afterStateHydrated(function ($component, $state) {
                    if (is_numeric($state)) {
                        $component->state(number_format((float)$state, 2, ',', '.'));
                    }
                })
                ->dehydrateStateUsing(function ($state) {
                    $cleanState = preg_replace('/[^\d,]/', '', $state);
                    return (float) str_replace(',', '.', $cleanState);
                }),

            Forms\Components\KeyValue::make('customization_options')
                ->label('Opções de Personalização')
                ->nullable()
                ->default(null)
                ->columnSpan('full')
                ->keyLabel('Opção')
                ->valueLabel('Descrição')
                ->reorderable()
                ->addActionLabel('Adicionar Opção')
                ->afterStateHydrated(function ($component, $state) {
                    if (is_string($state)) {
                        try {
                            $decoded = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $component->state($decoded);
                            } else {
                                $component->state([]);
                            }
                        } catch (\Exception $e) {
                            $component->state([]);
                        }
                    }
                })
                ->dehydrateStateUsing(function ($state) {
                    if (empty($state)) {
                        return null;
                    }
                    return json_encode($state);
                }),

            Forms\Components\KeyValue::make('file_requirements')
                ->label('Requisitos do Arquivo')
                ->nullable()
                ->default(null)
                ->columnSpan('full')
                ->keyLabel('Requisito')
                ->valueLabel('Descrição')
                ->reorderable()
                ->addActionLabel('Adicionar Requisito')
                ->afterStateHydrated(function ($component, $state) {
                    if (is_string($state)) {
                        try {
                            $decoded = json_decode($state, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $component->state($decoded);
                            } else {
                                $component->state([]);
                            }
                        } catch (\Exception $e) {
                            $component->state([]);
                        }
                    }
                })
                ->dehydrateStateUsing(function ($state) {
                    if (empty($state)) {
                        return null;
                    }
                    return json_encode($state);
                }),
        ]);
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record, $data) {
                        // Atualiza o total do orçamento
                        $quote = $record->quote;
                        $quote->total_amount = $quote->items()->sum('total_price');
                        $quote->save();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record, $data) {
                        // Atualiza o total do orçamento
                        $quote = $record->quote;
                        $quote->total_amount = $quote->items()->sum('total_price');
                        $quote->save();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        // Atualiza o total do orçamento
                        $quote = $record->quote;
                        $quote->total_amount = $quote->items()->sum('total_price');
                        $quote->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            // Atualiza o total do orçamento
                            $this->getOwnerRecord()->update([
                                'total_amount' => $this->getOwnerRecord()->items()->sum('total_price')
                            ]);
                        }),
                ]),
            ]);
    }
} 