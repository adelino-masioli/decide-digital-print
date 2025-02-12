<div>
    @if($showModal) 
    <div class="fixed inset-0 flex items-center justify-center custom-modal-overlay">
        <div class="max-w-2xl p-6 mx-auto text-sm bg-white rounded-lg">
            <h2 class="mb-4 text-lg font-bold">Bem-vindo ao Decide Digital Print!</h2>
            <p class="mb-4">Agradecemos por escolher nossa plataforma para aprimorar a gestão do seu negócio de impressão. O Decide Digital Print foi projetado para tornar seu dia a dia mais eficiente, organizando de maneira simples e intuitiva todos os processos essenciais da sua empresa.</p>
            <h3 class="mb-4 text-lg font-bold">Com esta ferramenta, você poderá:</h3>
            <p class="mb-2">Gerenciar seus clientes de forma ágil, com fácil acesso a todos os dados e possibilidade de exportação em PDF para relatórios e análises.</p>
            <p class="mb-2">Controlar os usuários da sua plataforma, garantindo que cada colaborador tenha as permissões necessárias, com exportação de PDFs para um acompanhamento completo.</p>
            <p class="mb-2">Organizar suas categorias de produtos, facilitando a visualização e gerenciamento, com exportação de PDF para um controle mais detalhado.</p>
            <p class="mb-2">Administrar seus produtos, desde a inserção até a atualização de informações, com exportação de PDF sempre que necessário.</p>
            <p class="mb-2">Gerenciar fornecedores de maneira prática, mantendo um controle preciso e exportando relatórios em PDF para uma visão estratégica.</p>
            <p class="mb-2">Utilizar um CRM compacto para acompanhar os relacionamentos com seus clientes, ajudando a manter tudo sob controle.</p>
            <p class="mb-2">Emitir orçamentos e enviá-los por email, com a facilidade de exportação em PDF para documentação e compartilhamento.</p>
            <p class="mb-2">Gerenciar pedidos, com envio de confirmações por email e exportação de PDFs, garantindo uma comunicação eficiente com seus clientes.</p>
            <p class="mb-2">Acompanhar o workflow de produção com a metodologia Kanban, mantendo sua equipe alinhada e seus processos otimizados.</p>
            <h4 class="mb-4 text-sm font-medium">Com o Decide Digital Print, sua gestão será mais ágil e organizada, permitindo que você foque no crescimento do seu negócio.</h4>
            <h4 class="mb-4 text-sm font-medium">Estamos aqui para facilitar sua jornada digital! Favor confirmar para fechar o modal.</h4>
            <div class="flex justify-end gap-2" style="margin-top: 20px;">
                <button wire:click="close" class="px-4 py-2 text-white bg-gray-400 rounded hover:bg-gray-500">Fechar</button>
                <button wire:click="confirm" class="px-4 py-2 text-white rounded bg-primary-600 hover:bg-blue-700">Confirmar</button>
            </div>
        </div>
    </div>
    @endif

    <style>
        .custom-modal-overlay {
            z-index: 99;
            background-color: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
        }
    </style>
</div> 