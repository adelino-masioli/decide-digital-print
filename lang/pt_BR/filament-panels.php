<?php

return [
    'pages' => [
        'auth' => [
            'register' => [
                'title' => 'Registrar',
                'heading' => 'Criar nova conta',
                'actions' => [
                    'login' => [
                        'before' => 'ou',
                        'label' => 'fazer login na sua conta',
                    ],
                ],
                'form' => [
                    'email' => [
                        'label' => 'Email',
                    ],
                    'name' => [
                        'label' => 'Nome',
                    ],
                    'password' => [
                        'label' => 'Senha',
                        'validation_attribute' => 'senha',
                    ],
                    'password_confirmation' => [
                        'label' => 'Confirmar senha',
                    ],
                ],
                'notifications' => [
                    'created' => [
                        'title' => 'Registrado com sucesso',
                    ],
                ],
            ],
            'login' => [
                'title' => 'Login',
                'heading' => 'Acessar sua conta',
                'form' => [
                    'email' => [
                        'label' => 'Email',
                    ],
                    'password' => [
                        'label' => 'Senha',
                    ],
                    'remember' => [
                        'label' => 'Lembrar-me',
                    ],
                ],
                'actions' => [
                    'register' => [
                        'before' => 'ou',
                        'label' => 'criar uma nova conta',
                    ],
                    'forgot_password' => [
                        'label' => 'Esqueceu sua senha?',
                    ],
                ],
            ],
            'forgot-password' => [
                'title' => 'Recuperar senha',
                'heading' => 'Recuperar senha',
                'form' => [
                    'email' => [
                        'label' => 'Email',
                    ],
                ],
                'notifications' => [
                    'sent' => [
                        'title' => 'Email enviado',
                        'body' => 'Enviamos um link para redefinir sua senha',
                    ],
                ],
            ],
            'reset-password' => [
                'title' => 'Redefinir senha',
                'heading' => 'Redefinir senha',
                'form' => [
                    'password' => [
                        'label' => 'Nova senha',
                    ],
                    'password_confirmation' => [
                        'label' => 'Confirmar nova senha',
                    ],
                ],
                'notifications' => [
                    'reset' => [
                        'title' => 'Senha redefinida',
                    ],
                ],
            ],
        ],
        'production_board' => [
            'label' => 'Quadro de Produção',
        ],
    ],
    'navigation' => [
        'groups' => [
            'settings' => [
                'label' => 'Configurações',
            ],
            'users' => [
                'label' => 'Usuários',
            ],
            'products' => [
                'label' => 'Produtos',
            ],
            'orders' => [
                'label' => 'Pedidos',
            ],
            'suppliers' => [
                'label' => 'Fornecedores',
            ],
            'customers' => [
                'label' => 'Clientes',
            ],
            'quotes' => [
                'label' => 'Orçamentos',
            ],
        ],
    ],
    'buttons' => [
        'save' => 'Salvar',
        'cancel' => 'Cancelar',
        'create' => 'Criar',
        'delete' => 'Excluir',
        'edit' => 'Editar',
        'update' => 'Atualizar',
        'search' => 'Buscar',
        'filter' => 'Filtrar',
        'open' => 'Abrir',
        'toggle_columns' => 'Alternar Colunas',
        'toggle_navigation' => 'Alternar navegação',
        'toggle_filters' => 'Alternar filtros',
        'view' => 'Visualizar',
        'attach' => 'Anexar',
        'detach' => 'Desanexar',
        'new' => 'Novo',
        'force_delete' => 'Forçar exclusão',
        'restore' => 'Restaurar',
        'enable' => 'Ativar',
        'disable' => 'Desativar',
    ],
    'actions' => [
        'modal' => [
            'requires_confirmation_subheading' => 'Tem certeza que deseja fazer isso?',
            'buttons' => [
                'confirm' => [
                    'label' => 'Confirmar',
                ],
                'cancel' => [
                    'label' => 'Cancelar',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'toggleable' => [
                'collapse' => 'Recolher colunas',
                'expand' => 'Expandir colunas',
            ],
        ],
        'empty' => [
            'heading' => 'Nenhum registro encontrado',
            'description' => 'Crie um registro para começar.',
        ],
        'filters' => [
            'heading' => 'Filtros',
            'indicator' => 'Filtros ativos',
            'multi_select' => [
                'placeholder' => 'Todos',
            ],
            'select' => [
                'placeholder' => 'Todos',
            ],
            'trashed' => [
                'label' => 'Registros excluídos',
                'only_trashed' => 'Somente registros excluídos',
                'with_trashed' => 'Com registros excluídos',
                'without_trashed' => 'Sem registros excluídos',
            ],
        ],
        'selection_indicator' => [
            'selected_count' => '1 registro selecionado.|:count registros selecionados.',
            'actions' => [
                'select_all' => [
                    'label' => 'Selecionar todos :count',
                ],
                'deselect_all' => [
                    'label' => 'Desmarcar todos',
                ],
            ],
        ],
    ],
    'messages' => [
        'required' => 'Obrigatório',
        'validation' => [
            'required' => 'O campo :attribute é obrigatório',
            'unique' => 'Este :attribute já está em uso',
        ],
    ],
    'resources' => [
        'forms' => [
            'actions' => [
                'create' => [
                    'label' => 'Criar :label',
                ],
                'edit' => [
                    'label' => 'Editar :label',
                ],
            ],
        ],
        'tables' => [
            'actions' => [
                'delete' => [
                    'label' => 'Excluir',
                ],
                'edit' => [
                    'label' => 'Editar',
                ],
                'view' => [
                    'label' => 'Visualizar',
                ],
            ],
            'bulk_actions' => [
                'delete' => [
                    'label' => 'Excluir selecionados',
                ],
            ],
        ],
        'labels' => [
            'Categories' => 'Categorias',
            'Category' => 'Categoria',
            'Products' => 'Produtos',
            'Product' => 'Produto',
            'Suppliers' => 'Fornecedores',
            'Supplier' => 'Fornecedor',
            'Supplies' => 'Suprimentos',
            'Supply' => 'Suprimento',
            'Quotes' => 'Orçamentos',
            'Quote' => 'Orçamento',
            'Orders' => 'Pedidos',
            'Order' => 'Pedido',
            'Users' => 'Usuários',
            'User' => 'Usuário',
        ],
        'fields' => [
            'name' => [
                'label' => 'Nome',
            ],
            'first_name' => [
                'label' => 'Nome',
            ],
            'last_name' => [
                'label' => 'Sobrenome',
            ],
            'email' => [
                'label' => 'Email',
            ],
            'phone' => [
                'label' => 'Telefone',
            ],
            'document' => [
                'label' => 'Documento',
            ],
            'is_active' => [
                'label' => 'Ativo',
            ],
            'status' => [
                'label' => 'Status',
            ],
            'description' => [
                'label' => 'Descrição',
            ],
            'price' => [
                'label' => 'Preço',
            ],
            'quantity' => [
                'label' => 'Quantidade',
            ],
            'date' => [
                'label' => 'Data',
            ],
            'created_at' => [
                'label' => 'Criado em',
            ],
            'updated_at' => [
                'label' => 'Atualizado em',
            ],
            'address' => [
                'label' => 'Endereço',
                'postal_code' => 'CEP',
                'street' => 'Rua',
                'number' => 'Número',
                'neighborhood' => 'Bairro',
                'complement' => 'Complemento',
            ],
            'city' => [
                'label' => 'Cidade',
            ],
            'state' => [
                'label' => 'Estado',
            ],
            'postal_code' => [
                'label' => 'CEP',
            ],
            'country' => [
                'label' => 'País',
            ],
        ],
        'values' => [
            'status' => [
                'active' => 'Ativo',
                'inactive' => 'Inativo',
                'pending' => 'Pendente',
                'pending_payment' => 'Pagamento Pendente',
                'cancelled' => 'Cancelado',
                'completed' => 'Concluído',
                'processing' => 'Em processamento',
            ],
            'is_active' => [
                'true' => 'Sim',
                'false' => 'Não',
            ],
        ],
        'table' => [
            'columns' => [
                'first_name' => 'Nome',
                'last_name' => 'Sobrenome',
                'document' => 'Documento',
                'phone' => 'Telefone',
                'email' => 'Email',
                'is_active' => 'Ativo',
                'created_at' => 'Criado em',
                'updated_at' => 'Atualizado em',
            ],
        ],
        'sections' => [
            'address' => 'Endereço',
        ],
        'status' => [
            'quotes' => [
                'draft' => 'Rascunho',
                'open' => 'Aberto',
                'approved' => 'Aprovado',
                'expired' => 'Expirado',
                'converted' => 'Convertido',
                'canceled' => 'Cancelado',
            ],
            'orders' => [
                'pending_payment' => 'Pagamento Pendente',
                'processing' => 'Em Processamento',
                'in_production' => 'Em Produção',
                'completed' => 'Concluído',
                'canceled' => 'Cancelado',
            ],
            'payment_method' => [
                'cash' => 'Dinheiro',
                'credit_card' => 'Cartão de Crédito',
                'pix' => 'PIX',
                'bank_slip' => 'Boleto',
            ],
            'payment_status' => [
                'pending' => 'Pendente',
                'paid' => 'Pago',
                'failed' => 'Falhou',
                'refunded' => 'Reembolsado',
            ],
        ],
    ],
    'common' => [
        'loading' => 'Carregando...',
        'no_results' => 'Nenhum resultado encontrado',
        'search' => [
            'label' => 'Pesquisar',
            'placeholder' => 'Pesquisar...',
        ],
        'sort' => [
            'asc' => 'Crescente',
            'desc' => 'Decrescente',
        ],
    ],
]; 