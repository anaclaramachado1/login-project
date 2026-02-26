<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Projeto de cobertura</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>
    <main class="dashboard-page">
        <header class="topbar-wrap">
            <div class="topbar">
                <div class="topbar-actions">
                    <div class="nav-buttons">
                        <form method="GET" action="/selecionar-ambiente">
                            <button type="submit" class="cadastro-btn">Trocar Ambiente</button>
                        </form>
                        <div class="cadastro-dropdown">
                            <button type="button" id="cadastro-toggle" class="cadastro-btn">Cadastro</button>
                            <div id="cadastro-menu" class="cadastro-menu is-hidden">
                                <button type="button" class="cadastro-option" data-target="clientes-fornecedores">Clientes e Fornecedores</button>
                                <button type="button" class="cadastro-option" data-target="itens">Itens</button>
                            </div>
                        </div>

                        <button type="button" id="cotacao-nav-btn" class="cadastro-btn">Cotacao</button>
                    </div>

                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="logout-btn">Sair</button>
                    </form>
                </div>
            </div>
        </header>

        <section class="content-area">
            <article id="panel-empty" class="content-empty">
                <p>Clique em <strong>Cadastro</strong> ou <strong>Cotacao</strong> para carregar a tela.</p>
            </article>

            <article id="panel-clientes-fornecedores" class="content-panel">
                <div class="content-toolbar">
                    <h2>Cadastro de Clientes e Fornecedores</h2>
                    <button type="button" class="open-cadastro-modal">Novo Cadastro</button>
                </div>
                <div class="list-search">
                    <span class="search-icon" aria-hidden="true"></span>
                    <input type="search" id="cf-search" placeholder="Pesquisar fornecedor, cliente, CNPJ/CPF, contato..." />
                </div>
                <p id="cf-status" class="form-status is-hidden"></p>
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome / Fantasia</th>
                                <th>CNPJ/CPF</th>
                                <th>Contato</th>
                                <th>Cidade/UF</th>
                                <th>Endereco</th>
                                <th>Acoes</th>
                            </tr>
                        </thead>
                        <tbody id="cf-table-body">
                            <tr>
                                <td colspan="6">Nenhum cadastro encontrado.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>

            <article id="panel-itens" class="content-panel">
                <div class="content-toolbar">
                    <h2>Cadastro de Itens</h2>
                    <div class="toolbar-actions">
                        <button type="button" class="open-unidade-modal">Unidades de Medida</button>
                        <button type="button" class="open-item-modal">Novo Item</button>
                    </div>
                </div>
                <div class="list-search">
                    <span class="search-icon" aria-hidden="true"></span>
                    <input type="search" id="item-search" placeholder="Pesquisar por codigo, descricao ou unidade..." />
                </div>
                <p id="item-status" class="form-status is-hidden"></p>
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Descricao</th>
                                <th>Unidade</th>
                                <th>Preco</th>
                                <th>Estoque</th>
                                <th>Acoes</th>
                            </tr>
                        </thead>
                        <tbody id="item-table-body">
                            <tr>
                                <td colspan="6">Nenhum item cadastrado ainda.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>

            <article id="panel-cotacao" class="content-panel">
                <div class="content-toolbar">
                    <h2>Cotacoes</h2>
                    <button type="button" id="nova-cotacao-btn" class="primary-inline-btn">Nova Cotacao</button>
                </div>
                <div class="list-search">
                    <span class="search-icon" aria-hidden="true"></span>
                    <input type="search" id="cotacao-search" placeholder="Pesquisar por numero, cliente ou data..." />
                </div>
                <p id="cotacao-list-status" class="form-status is-hidden"></p>

                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Numero</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Itens</th>
                                <th>Total</th>
                                <th>Acoes</th>
                            </tr>
                        </thead>
                        <tbody id="cotacao-list-body">
                            <tr>
                                <td colspan="6">Nenhuma cotacao cadastrada ainda.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </main>

    <div id="cadastro-modal" class="modal is-hidden" aria-hidden="true">
        <div class="modal-backdrop" data-close-modal></div>
        <section class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <header class="modal-header">
                <h3 id="modal-title">Novo Cadastro de Cliente e Fornecedor</h3>
                <button type="button" class="modal-close" data-close-modal aria-label="Fechar">x</button>
            </header>

            <p id="cadastro-modal-status" class="form-status is-hidden"></p>

            <form id="cadastro-form" class="cadastro-form">
                <fieldset class="form-section">
                    <legend>Identificacao</legend>

                    <label>
                        Nome/Razao Social:
                        <input type="text" name="nome_razao_social" required />
                    </label>
                    <label>
                        Nome Fantasia:
                        <input type="text" name="nome_fantasia" />
                    </label>
                    <label>
                        CNPJ/CPF:
                        <input type="text" name="cnpj_cpf" />
                    </label>
                    <label>
                        IE/RG:
                        <input type="text" name="ie_rg" />
                    </label>
                </fieldset>

                <fieldset class="form-section">
                    <legend>Contato</legend>

                    <label>
                        Telefone de Contato:
                        <input type="text" name="telefone_contato" />
                    </label>
                    <label>
                        Email de Contato:
                        <input type="email" name="email_contato" />
                    </label>
                </fieldset>

                <fieldset class="form-section">
                    <legend>Endereco</legend>

                    <label>
                        CEP:
                        <input type="text" name="cep" />
                    </label>
                    <label>
                        Endereco:
                        <input type="text" name="endereco" />
                    </label>
                    <label>
                        Numero:
                        <input type="text" name="numero" />
                    </label>
                    <label>
                        Compl.:
                        <input type="text" name="complemento" />
                    </label>
                    <label>
                        Bairro:
                        <input type="text" name="bairro" />
                    </label>
                    <label>
                        Cidade:
                        <input type="text" name="cidade" />
                    </label>
                    <label>
                        UF:
                        <select name="uf">
                            <option value="">Selecione</option>
                            <option value="AC">AC</option>
                            <option value="AL">AL</option>
                            <option value="AP">AP</option>
                            <option value="AM">AM</option>
                            <option value="BA">BA</option>
                            <option value="CE">CE</option>
                            <option value="DF">DF</option>
                            <option value="ES">ES</option>
                            <option value="GO">GO</option>
                            <option value="MA">MA</option>
                            <option value="MT">MT</option>
                            <option value="MS">MS</option>
                            <option value="MG">MG</option>
                            <option value="PA">PA</option>
                            <option value="PB">PB</option>
                            <option value="PR">PR</option>
                            <option value="PE">PE</option>
                            <option value="PI">PI</option>
                            <option value="RJ">RJ</option>
                            <option value="RN">RN</option>
                            <option value="RS">RS</option>
                            <option value="RO">RO</option>
                            <option value="RR">RR</option>
                            <option value="SC">SC</option>
                            <option value="SP">SP</option>
                            <option value="SE">SE</option>
                            <option value="TO">TO</option>
                        </select>
                    </label>
                </fieldset>

                <div class="modal-actions">
                    <button type="button" class="secondary" data-close-modal>Cancelar</button>
                    <button type="submit" id="save-cadastro-btn" class="primary">Salvar</button>
                </div>
            </form>
        </section>
    </div>

    <div id="item-modal" class="modal is-hidden" aria-hidden="true">
        <div class="modal-backdrop" data-close-item-modal></div>
        <section class="modal-card" role="dialog" aria-modal="true" aria-labelledby="item-modal-title">
            <header class="modal-header">
                <h3 id="item-modal-title">Novo Item</h3>
                <button type="button" class="modal-close" data-close-item-modal aria-label="Fechar">x</button>
            </header>

            <p id="item-modal-status" class="form-status is-hidden"></p>

            <form id="item-form" class="item-form">
                <label>
                    Codigo:
                    <input type="text" name="codigo" id="item-codigo" readonly required />
                </label>
                <label>
                    Descricao:
                    <input type="text" name="descricao" required />
                </label>
                <label>
                    Unidade de Medida:
                    <select name="unidade_medida" id="item-unidade-medida" required>
                        <option value="">Selecione</option>
                    </select>
                </label>
                <label>
                    Preco:
                    <input type="number" name="preco" min="0" step="0.01" required />
                </label>
                <label>
                    Estoque:
                    <input type="number" name="estoque" min="0" step="1" required />
                </label>

                <div class="modal-actions">
                    <button type="button" class="secondary" data-close-item-modal>Cancelar</button>
                    <button type="submit" id="save-item-btn" class="primary">Salvar</button>
                </div>
            </form>
        </section>
    </div>

    <div id="unidade-modal" class="modal is-hidden" aria-hidden="true">
        <div class="modal-backdrop" data-close-unidade-modal></div>
        <section class="modal-card" role="dialog" aria-modal="true" aria-labelledby="unidade-modal-title">
            <header class="modal-header">
                <h3 id="unidade-modal-title">Cadastro de Unidades de Medida</h3>
                <button type="button" class="modal-close" data-close-unidade-modal aria-label="Fechar">x</button>
            </header>

            <p id="unidade-status" class="form-status is-hidden"></p>

            <form id="unidade-form" class="item-form">
                <label>
                    Sigla:
                    <input type="text" name="sigla" maxlength="20" placeholder="UN, KG, CX..." required />
                </label>
                <label>
                    Descricao:
                    <input type="text" name="descricao" maxlength="100" placeholder="Ex.: Unidade, Quilograma..." />
                </label>

                <div class="modal-actions">
                    <button type="button" class="secondary" data-close-unidade-modal>Cancelar</button>
                    <button type="submit" id="save-unidade-btn" class="primary">Salvar</button>
                </div>
            </form>

            <div class="list-search list-search-inline">
                <span class="search-icon" aria-hidden="true"></span>
                <input type="search" id="unidade-search" placeholder="Pesquisar unidade..." />
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>Sigla</th>
                            <th>Descricao</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody id="unidade-table-body">
                        <tr>
                            <td colspan="3">Nenhuma unidade cadastrada.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="confirm-modal" class="modal is-hidden" aria-hidden="true">
        <div class="modal-backdrop" data-close-confirm-modal></div>
        <section class="modal-card confirm-card" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
            <header class="modal-header">
                <h3 id="confirm-modal-title">Confirmar acao</h3>
                <button type="button" class="modal-close" data-close-confirm-modal aria-label="Fechar">x</button>
            </header>
            <p id="confirm-modal-message" class="confirm-message"></p>
            <div class="modal-actions">
                <button type="button" class="secondary" id="confirm-cancel-btn">Cancelar</button>
                <button type="button" class="primary" id="confirm-ok-btn">Confirmar</button>
            </div>
        </section>
    </div>

    <div id="cotacao-detail-modal" class="modal is-hidden" aria-hidden="true">
        <div class="modal-backdrop" data-close-cotacao-detail-modal></div>
        <section class="modal-card" role="dialog" aria-modal="true" aria-labelledby="cotacao-detail-title">
            <header class="modal-header">
                <h3 id="cotacao-detail-title">Detalhes da Cotacao</h3>
                <button type="button" class="modal-close" data-close-cotacao-detail-modal aria-label="Fechar">x</button>
            </header>

            <p id="cotacao-detail-status" class="form-status is-hidden"></p>

            <div class="cotacao-detail-header">
                <div><strong>Numero:</strong> <span id="cotacao-detail-numero">-</span></div>
                <div><strong>Cliente:</strong> <span id="cotacao-detail-cliente">-</span></div>
                <div><strong>Data:</strong> <span id="cotacao-detail-data">-</span></div>
                <div><strong>Total:</strong> <span id="cotacao-detail-total">R$ 0,00</span></div>
            </div>

            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Descricao</th>
                            <th>Unidade</th>
                            <th>Preco</th>
                            <th>Qtd</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="cotacao-detail-itens-body">
                        <tr>
                            <td colspan="6">Nenhum item encontrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p><strong>Observacoes:</strong> <span id="cotacao-detail-observacoes">-</span></p>
        </section>
    </div>

    <div id="cotacao-create-modal" class="modal is-hidden" aria-hidden="true">
        <div class="modal-backdrop" data-close-cotacao-create-modal></div>
        <section class="modal-card cotacao-create-card" role="dialog" aria-modal="true" aria-labelledby="cotacao-create-title">
            <header class="modal-header">
                <h3 id="cotacao-create-title">Nova Cotacao</h3>
                <button type="button" class="modal-close" data-close-cotacao-create-modal aria-label="Fechar">x</button>
            </header>
            <p id="cotacao-create-status" class="form-status is-hidden"></p>

            <div class="cotacao-shell">
                <form id="cotacao-form" class="cotacao-form">
                    <label>
                        Numero:
                        <input type="text" id="cotacao-numero" name="numero" readonly required />
                    </label>
                    <label>
                        Cliente:
                        <select id="cotacao-cliente" name="cliente_fornecedor_id" required>
                            <option value="">Selecione</option>
                        </select>
                    </label>
                    <label>
                        Data:
                        <input type="date" id="cotacao-data" name="data_emissao" required />
                    </label>
                    <label class="cotacao-full">
                        Observacoes:
                        <input type="text" id="cotacao-observacoes" name="observacoes" placeholder="Opcional" />
                    </label>
                </form>

                <div class="cotacao-item-builder">
                    <label class="cotacao-item-field">
                        Item:
                        <input type="text" id="cotacao-item-select" placeholder="Digite para buscar..." autocomplete="off" />
                        <div id="cotacao-item-suggestions" class="autocomplete-list is-hidden"></div>
                    </label>
                    <label>
                        Quantidade:
                        <input type="number" id="cotacao-item-quantidade" min="1" step="1" value="1" />
                    </label>
                    <button type="button" id="cotacao-add-item-btn" class="primary-inline-btn">Adicionar Item</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Descricao</th>
                            <th>Unidade</th>
                            <th>Preco</th>
                            <th>Qtd</th>
                            <th>Total</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody id="cotacao-itens-body">
                        <tr>
                            <td colspan="7">Nenhum item adicionado.</td>
                        </tr>
                    </tbody>
                </table>

                <div class="cotacao-footer">
                    <strong>Total Geral: <span id="cotacao-total-geral">R$ 0,00</span></strong>
                    <div class="toolbar-actions">
                        <button type="button" id="cotacao-limpar-btn" class="secondary-inline-btn">Limpar</button>
                        <button type="button" id="cotacao-salvar-btn" class="primary-inline-btn">Salvar Cotacao</button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const cadastroToggle = document.getElementById('cadastro-toggle');
        const cadastroMenu = document.getElementById('cadastro-menu');
        const cotacaoNavBtn = document.getElementById('cotacao-nav-btn');
        const cotacaoListStatus = document.getElementById('cotacao-list-status');
        const cotacaoForm = document.getElementById('cotacao-form');
        const novaCotacaoBtn = document.getElementById('nova-cotacao-btn');
        const cotacaoCreateModal = document.getElementById('cotacao-create-modal');
        const cotacaoCreateCloseButtons = document.querySelectorAll('[data-close-cotacao-create-modal]');
        const cotacaoCreateStatus = document.getElementById('cotacao-create-status');
        const cotacaoNumeroInput = document.getElementById('cotacao-numero');
        const cotacaoClienteSelect = document.getElementById('cotacao-cliente');
        const cotacaoDataInput = document.getElementById('cotacao-data');
        const cotacaoObservacoesInput = document.getElementById('cotacao-observacoes');
        const cotacaoItemSelect = document.getElementById('cotacao-item-select');
        const cotacaoItemSuggestions = document.getElementById('cotacao-item-suggestions');
        const cotacaoItemQuantidadeInput = document.getElementById('cotacao-item-quantidade');
        const cotacaoAddItemBtn = document.getElementById('cotacao-add-item-btn');
        const cotacaoItensBody = document.getElementById('cotacao-itens-body');
        const cotacaoTotalGeral = document.getElementById('cotacao-total-geral');
        const cotacaoLimparBtn = document.getElementById('cotacao-limpar-btn');
        const cotacaoSalvarBtn = document.getElementById('cotacao-salvar-btn');
        const cotacaoListBody = document.getElementById('cotacao-list-body');
        const cfSearchInput = document.getElementById('cf-search');
        const itemSearchInput = document.getElementById('item-search');
        const cotacaoSearchInput = document.getElementById('cotacao-search');
        const unidadeSearchInput = document.getElementById('unidade-search');
        const cotacaoDetailModal = document.getElementById('cotacao-detail-modal');
        const cotacaoDetailCloseButtons = document.querySelectorAll('[data-close-cotacao-detail-modal]');
        const cotacaoDetailStatus = document.getElementById('cotacao-detail-status');
        const cotacaoDetailNumero = document.getElementById('cotacao-detail-numero');
        const cotacaoDetailCliente = document.getElementById('cotacao-detail-cliente');
        const cotacaoDetailData = document.getElementById('cotacao-detail-data');
        const cotacaoDetailTotal = document.getElementById('cotacao-detail-total');
        const cotacaoDetailItensBody = document.getElementById('cotacao-detail-itens-body');
        const cotacaoDetailObservacoes = document.getElementById('cotacao-detail-observacoes');
        const links = document.querySelectorAll('.cadastro-option');
        const panels = document.querySelectorAll('.content-panel');
        const emptyPanel = document.getElementById('panel-empty');
        const cfTableBody = document.getElementById('cf-table-body');
        const cfStatus = document.getElementById('cf-status');
        const cadastroModalStatus = document.getElementById('cadastro-modal-status');
        const itemTableBody = document.getElementById('item-table-body');
        const itemStatus = document.getElementById('item-status');
        const itemModalStatus = document.getElementById('item-modal-status');
        const unidadeStatus = document.getElementById('unidade-status');
        const unidadeTableBody = document.getElementById('unidade-table-body');
        const modal = document.getElementById('cadastro-modal');
        const modalOpenButtons = document.querySelectorAll('.open-cadastro-modal');
        const modalCloseButtons = document.querySelectorAll('[data-close-modal]');
        const cadastroForm = document.getElementById('cadastro-form');
        const saveCadastroBtn = document.getElementById('save-cadastro-btn');
        const modalTitle = document.getElementById('modal-title');
        const itemModal = document.getElementById('item-modal');
        const itemOpenButtons = document.querySelectorAll('.open-item-modal');
        const itemCloseButtons = document.querySelectorAll('[data-close-item-modal]');
        const itemForm = document.getElementById('item-form');
        const saveItemBtn = document.getElementById('save-item-btn');
        const itemModalTitle = document.getElementById('item-modal-title');
        const itemCodigoInput = document.getElementById('item-codigo');
        const itemUnidadeSelect = document.getElementById('item-unidade-medida');
        const unidadeModal = document.getElementById('unidade-modal');
        const unidadeOpenButtons = document.querySelectorAll('.open-unidade-modal');
        const unidadeCloseButtons = document.querySelectorAll('[data-close-unidade-modal]');
        const unidadeForm = document.getElementById('unidade-form');
        const saveUnidadeBtn = document.getElementById('save-unidade-btn');
        const confirmModal = document.getElementById('confirm-modal');
        const confirmCloseButtons = document.querySelectorAll('[data-close-confirm-modal]');
        const confirmMessage = document.getElementById('confirm-modal-message');
        const confirmOkBtn = document.getElementById('confirm-ok-btn');
        const confirmCancelBtn = document.getElementById('confirm-cancel-btn');
        const cepInput = document.querySelector('input[name="cep"]');
        const enderecoInput = document.querySelector('input[name="endereco"]');
        const bairroInput = document.querySelector('input[name="bairro"]');
        const cidadeInput = document.querySelector('input[name="cidade"]');
        const ufSelect = document.querySelector('select[name="uf"]');
        let loadedCfOnce = false;
        let loadedItemsOnce = false;
        let loadedUnidadesOnce = false;
        let loadedCotacoesOnce = false;
        let loadedCotacaoContextoOnce = false;
        let editingId = null;
        let editingItemId = null;
        let editingUnidadeId = null;
        let confirmResolver = null;
        let cfItems = [];
        let itemItems = [];
        let unidadesMedida = [];
        let cotacaoItems = [];
        let cotacaoClientes = [];
        let cotacaoItensDisponiveis = [];
        let cotacaoItensSelecionados = [];
        let cotacaoSelectedItemId = null;
        let cotacaoSuggestionIndex = -1;

        cadastroToggle.addEventListener('click', () => {
            cadastroMenu.classList.toggle('is-hidden');
        });

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        function normalizeSearch(value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        }

        function matchesSearch(haystack, query) {
            if (!query) {
                return true;
            }

            return normalizeSearch(haystack).includes(query);
        }

        function applyCfFilter() {
            const query = normalizeSearch(cfSearchInput?.value || '');
            const filtered = !query
                ? cfItems
                : cfItems.filter((item) => {
                    const cidadeUf = [item.cidade, item.uf].filter(Boolean).join(' ');
                    const endereco = [item.endereco, item.numero, item.bairro, item.complemento].filter(Boolean).join(' ');

                    return matchesSearch([
                        item.nome_razao_social,
                        item.nome_fantasia,
                        item.cnpj_cpf,
                        item.ie_rg,
                        item.telefone_contato,
                        item.email_contato,
                        cidadeUf,
                        endereco,
                    ].join(' '), query);
                });

            renderCfRows(filtered);
        }

        function applyItemFilter() {
            const query = normalizeSearch(itemSearchInput?.value || '');
            const filtered = !query
                ? itemItems
                : itemItems.filter((item) => matchesSearch([
                    item.codigo,
                    item.descricao,
                    item.unidade_medida,
                    item.preco,
                    item.estoque,
                ].join(' '), query));

            renderItemRows(filtered);
        }

        function applyUnidadeFilter() {
            const query = normalizeSearch(unidadeSearchInput?.value || '');
            const filtered = !query
                ? unidadesMedida
                : unidadesMedida.filter((item) => matchesSearch([
                    item.sigla,
                    item.descricao,
                ].join(' '), query));

            renderUnidadesRows(filtered);
        }

        function applyCotacaoFilter() {
            const query = normalizeSearch(cotacaoSearchInput?.value || '');
            const filtered = !query
                ? cotacaoItems
                : cotacaoItems.filter((item) => {
                    const dataFormatada = formatDate(item.data_emissao || '');
                    return matchesSearch([
                        item.numero,
                        item.cliente_nome,
                        item.data_emissao,
                        dataFormatada,
                        item.itens_count,
                        item.total_geral,
                    ].join(' '), query);
                });

            renderCotacoesList(filtered);
        }

        function renderCfRows(items) {
            if (!items.length) {
                cfTableBody.innerHTML = '<tr><td colspan="6">Nenhum cadastro encontrado.</td></tr>';
                return;
            }

            cfTableBody.innerHTML = items.map((item) => {
                const cidadeUf = [item.cidade, item.uf].filter(Boolean).join('/');
                const endereco = [item.endereco, item.numero].filter(Boolean).join(', ');
                const contato = [item.telefone_contato, item.email_contato].filter(Boolean);
                const nomeSecundario = item.nome_fantasia || '-';

                return `
                    <tr>
                        <td>
                            <div class="cf-main-name">${escapeHtml(item.nome_razao_social)}</div>
                            <div class="cf-secondary">Fantasia: ${escapeHtml(nomeSecundario)}</div>
                        </td>
                        <td>${escapeHtml(item.cnpj_cpf || '-')}</td>
                        <td>${escapeHtml(contato.length ? contato.join(' | ') : '-')}</td>
                        <td>${escapeHtml(cidadeUf || '-')}</td>
                        <td>${escapeHtml(endereco || '-')}</td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="row-edit-btn" data-id="${item.id}">Editar</button>
                                <button type="button" class="row-delete-btn cf-delete-btn" data-id="${item.id}">Excluir</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function formatCurrency(value) {
            return Number(value || 0).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL',
            });
        }

        function renderItemRows(items) {
            if (!items.length) {
                itemTableBody.innerHTML = '<tr><td colspan="6">Nenhum item cadastrado ainda.</td></tr>';
                return;
            }

            itemTableBody.innerHTML = items.map((item) => {
                const emUso = Boolean(Number(item.em_uso) || item.em_uso === true);
                const disabledAttr = emUso ? 'disabled title="Item em uso em outros registros"' : '';

                return `
                <tr>
                    <td>${escapeHtml(item.codigo)}</td>
                    <td>${escapeHtml(item.descricao)}</td>
                    <td>${escapeHtml(item.unidade_medida || '-')}</td>
                    <td>${escapeHtml(formatCurrency(item.preco))}</td>
                    <td>${escapeHtml(item.estoque)}</td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="row-edit-btn item-edit-btn" data-id="${item.id}" ${disabledAttr}>Editar</button>
                            <button type="button" class="row-delete-btn item-delete-btn" data-id="${item.id}" ${disabledAttr}>Excluir</button>
                        </div>
                    </td>
                </tr>
            `;
            }).join('');
        }

        function renderUnidadesRows(items) {
            if (!items.length) {
                unidadeTableBody.innerHTML = '<tr><td colspan="3">Nenhuma unidade cadastrada.</td></tr>';
                return;
            }

            unidadeTableBody.innerHTML = items.map((item) => {
                const emUso = Boolean(Number(item.em_uso) || item.em_uso === true);
                const disabledAttr = emUso ? 'disabled title="Unidade em uso por item cadastrado"' : '';

                return `
                <tr>
                    <td>${escapeHtml(item.sigla)}</td>
                    <td>${escapeHtml(item.descricao || '-')}</td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="row-edit-btn unidade-edit-btn" data-id="${item.id}" ${disabledAttr}>Editar</button>
                            <button type="button" class="row-delete-btn unidade-delete-btn" data-id="${item.id}" ${disabledAttr}>Excluir</button>
                        </div>
                    </td>
                </tr>
            `;
            }).join('');
        }

        function fillUnidadeSelect(items) {
            const currentValue = itemUnidadeSelect.value;
            const options = ['<option value="">Selecione</option>'];

            items.forEach((item) => {
                options.push(`<option value="${escapeHtml(item.sigla)}">${escapeHtml(item.sigla)} - ${escapeHtml(item.descricao || '')}</option>`);
            });

            itemUnidadeSelect.innerHTML = options.join('');
            itemUnidadeSelect.value = items.some((item) => item.sigla === currentValue) ? currentValue : '';
        }

        function formatDate(value) {
            if (!value) {
                return '-';
            }

            const parts = String(value).split('-');
            if (parts.length !== 3) {
                return value;
            }

            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }

        function fillCotacaoClientes(clientes) {
            const options = ['<option value="">Selecione</option>'];
            clientes.forEach((cliente) => {
                const nomeFantasia = cliente.nome_fantasia ? ` (${cliente.nome_fantasia})` : '';
                options.push(`<option value="${cliente.id}">${escapeHtml(`${cliente.nome_razao_social}${nomeFantasia}`)}</option>`);
            });
            cotacaoClienteSelect.innerHTML = options.join('');
        }

        function getCotacaoItemLabel(item) {
            return `${item.codigo} - ${item.descricao} (${formatCurrency(item.preco)})`;
        }

        function getCotacaoItemMatches(query) {
            const termo = (query || '').trim().toLowerCase();
            const lista = termo
                ? cotacaoItensDisponiveis.filter((item) => {
                    const codigo = String(item.codigo || '').toLowerCase();
                    const descricao = String(item.descricao || '').toLowerCase();
                    const unidade = String(item.unidade_medida || '').toLowerCase();
                    return codigo.includes(termo) || descricao.includes(termo) || unidade.includes(termo);
                })
                : cotacaoItensDisponiveis;

            return lista.slice(0, 12);
        }

        function renderCotacaoItemSuggestions(query = '', shouldOpen = false) {
            const matches = getCotacaoItemMatches(query);

            if (!matches.length) {
                cotacaoItemSuggestions.innerHTML = '';
                cotacaoItemSuggestions.classList.add('is-hidden');
                cotacaoSuggestionIndex = -1;
                return;
            }

            cotacaoItemSuggestions.innerHTML = matches.map((item) => `
                <button type="button" class="autocomplete-option" data-id="${item.id}">
                    ${escapeHtml(getCotacaoItemLabel(item))}
                </button>
            `).join('');
            cotacaoSuggestionIndex = -1;

            if (shouldOpen) {
                cotacaoItemSuggestions.classList.remove('is-hidden');
            } else {
                cotacaoItemSuggestions.classList.add('is-hidden');
            }
        }

        function getCotacaoSuggestionButtons() {
            return Array.from(cotacaoItemSuggestions.querySelectorAll('.autocomplete-option'));
        }

        function updateCotacaoSuggestionHighlight() {
            const buttons = getCotacaoSuggestionButtons();
            buttons.forEach((button, index) => {
                button.classList.toggle('is-active', index === cotacaoSuggestionIndex);
            });
        }

        function applyCotacaoSuggestionSelection(button) {
            const itemId = Number(button?.dataset.id || 0);
            const item = cotacaoItensDisponiveis.find((entry) => Number(entry.id) === itemId);
            if (!item) {
                return;
            }

            cotacaoSelectedItemId = itemId;
            cotacaoItemSelect.value = getCotacaoItemLabel(item);
            cotacaoSuggestionIndex = -1;
            cotacaoItemSuggestions.classList.add('is-hidden');
        }

        function fillCotacaoItens(itens) {
            cotacaoSelectedItemId = null;
            renderCotacaoItemSuggestions(cotacaoItemSelect.value, false);
        }

        function renderCotacaoItensSelecionados() {
            if (!cotacaoItensSelecionados.length) {
                cotacaoItensBody.innerHTML = '<tr><td colspan="7">Nenhum item adicionado.</td></tr>';
                cotacaoTotalGeral.textContent = formatCurrency(0);
                return;
            }

            let total = 0;
            cotacaoItensBody.innerHTML = cotacaoItensSelecionados.map((item) => {
                const totalItem = Number(item.preco) * Number(item.quantidade);
                total += totalItem;

                return `
                    <tr>
                        <td>${escapeHtml(item.codigo)}</td>
                        <td>${escapeHtml(item.descricao)}</td>
                        <td>${escapeHtml(item.unidade_medida || '-')}</td>
                        <td>${escapeHtml(formatCurrency(item.preco))}</td>
                        <td>${escapeHtml(item.quantidade)}</td>
                        <td>${escapeHtml(formatCurrency(totalItem))}</td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="row-delete-btn cotacao-remove-item-btn" data-id="${item.id}">Remover</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            cotacaoTotalGeral.textContent = formatCurrency(total);
        }

        function renderCotacoesList(items) {
            if (!items.length) {
                cotacaoListBody.innerHTML = '<tr><td colspan="6">Nenhuma cotacao cadastrada ainda.</td></tr>';
                return;
            }

            cotacaoListBody.innerHTML = items.map((item) => `
                <tr>
                    <td>${escapeHtml(item.numero)}</td>
                    <td>${escapeHtml(item.cliente_nome || '-')}</td>
                    <td>${escapeHtml(formatDate(item.data_emissao))}</td>
                    <td>${escapeHtml(item.itens_count ?? 0)}</td>
                    <td>${escapeHtml(formatCurrency(item.total_geral))}</td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="row-edit-btn cotacao-view-btn" data-id="${item.id}">Ver</button>
                            <button type="button" class="row-delete-btn cotacao-delete-btn" data-id="${item.id}">Excluir</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        async function parseApiResponse(response) {
            const contentType = response.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                return await response.json();
            }

            const text = await response.text();
            return {
                message: text.includes('<!DOCTYPE') ? 'Resposta invalida do servidor.' : text,
            };
        }

        function closeModal() {
            if (document.activeElement instanceof HTMLElement) {
                document.activeElement.blur();
            }

            modal.classList.add('is-hidden');
            modal.setAttribute('aria-hidden', 'true');
            cadastroForm.reset();
            cadastroModalStatus.classList.add('is-hidden');
            editingId = null;
            modalTitle.textContent = 'Novo Cadastro de Cliente e Fornecedor';
            saveCadastroBtn.textContent = 'Salvar';
        }

        function closeItemModal() {
            if (document.activeElement instanceof HTMLElement) {
                document.activeElement.blur();
            }

            itemModal.classList.add('is-hidden');
            itemModal.setAttribute('aria-hidden', 'true');
            itemForm.reset();
            itemModalStatus.classList.add('is-hidden');
            editingItemId = null;
            itemModalTitle.textContent = 'Novo Item';
            saveItemBtn.textContent = 'Salvar';
        }

        function closeUnidadeModal() {
            if (document.activeElement instanceof HTMLElement) {
                document.activeElement.blur();
            }

            unidadeModal.classList.add('is-hidden');
            unidadeModal.setAttribute('aria-hidden', 'true');
            unidadeForm.reset();
            editingUnidadeId = null;
            unidadeStatus.classList.add('is-hidden');
            saveUnidadeBtn.textContent = 'Salvar';
        }

        function closeConfirmModal(confirmed) {
            if (document.activeElement instanceof HTMLElement) {
                document.activeElement.blur();
            }

            confirmModal.classList.add('is-hidden');
            confirmModal.setAttribute('aria-hidden', 'true');

            if (confirmResolver) {
                confirmResolver(Boolean(confirmed));
                confirmResolver = null;
            }
        }

        function openConfirmModal(message) {
            confirmMessage.textContent = message;
            confirmModal.classList.remove('is-hidden');
            confirmModal.setAttribute('aria-hidden', 'false');

            return new Promise((resolve) => {
                confirmResolver = resolve;
            });
        }

        function closeCotacaoDetailModal() {
            if (document.activeElement instanceof HTMLElement) {
                document.activeElement.blur();
            }

            cotacaoDetailModal.classList.add('is-hidden');
            cotacaoDetailModal.setAttribute('aria-hidden', 'true');
            cotacaoDetailStatus.classList.add('is-hidden');
            cotacaoDetailNumero.textContent = '-';
            cotacaoDetailCliente.textContent = '-';
            cotacaoDetailData.textContent = '-';
            cotacaoDetailTotal.textContent = formatCurrency(0);
            cotacaoDetailObservacoes.textContent = '-';
            cotacaoDetailItensBody.innerHTML = '<tr><td colspan="6">Nenhum item encontrado.</td></tr>';
        }

        function closeCotacaoCreateModal() {
            if (document.activeElement instanceof HTMLElement) {
                document.activeElement.blur();
            }

            cotacaoCreateModal.classList.add('is-hidden');
            cotacaoCreateModal.setAttribute('aria-hidden', 'true');
            cotacaoCreateStatus.classList.add('is-hidden');
        }

        function renderCotacaoDetail(cotacao, itens) {
            cotacaoDetailNumero.textContent = cotacao?.numero || '-';
            cotacaoDetailCliente.textContent = cotacao?.cliente_nome || '-';
            cotacaoDetailData.textContent = formatDate(cotacao?.data_emissao || '');
            cotacaoDetailTotal.textContent = formatCurrency(cotacao?.total_geral || 0);
            cotacaoDetailObservacoes.textContent = cotacao?.observacoes || '-';

            if (!itens.length) {
                cotacaoDetailItensBody.innerHTML = '<tr><td colspan="6">Nenhum item encontrado.</td></tr>';
                return;
            }

            cotacaoDetailItensBody.innerHTML = itens.map((item) => `
                <tr>
                    <td>${escapeHtml(item.item_codigo)}</td>
                    <td>${escapeHtml(item.descricao)}</td>
                    <td>${escapeHtml(item.unidade_medida || '-')}</td>
                    <td>${escapeHtml(formatCurrency(item.preco_unitario))}</td>
                    <td>${escapeHtml(item.quantidade)}</td>
                    <td>${escapeHtml(formatCurrency(item.total_item))}</td>
                </tr>
            `).join('');
        }

        function getApiErrorMessage(data, fallback) {
            if (data?.errors) {
                const firstField = Object.keys(data.errors)[0];
                const firstMessage = data.errors[firstField]?.[0];

                if (firstMessage) {
                    return firstMessage;
                }
            }

            return data?.message || fallback;
        }

        async function loadClientesFornecedores() {
            cfStatus.classList.add('is-hidden');

            try {
                const response = await fetch('/api/clientes-fornecedores', {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel carregar os dados.'));
                }

                cfItems = data.items || [];
                applyCfFilter();
                loadedCfOnce = true;
            } catch (error) {
                cfStatus.textContent = error.message;
                cfStatus.classList.remove('is-hidden');
            }
        }

        async function loadItens() {
            itemStatus.classList.add('is-hidden');

            try {
                const response = await fetch('/api/itens', {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel carregar os itens.'));
                }

                itemItems = data.items || [];
                applyItemFilter();
                loadedItemsOnce = true;
            } catch (error) {
                itemStatus.textContent = error.message;
                itemStatus.classList.remove('is-hidden');
            }
        }

        async function loadUnidadesMedida() {
            unidadeStatus.classList.add('is-hidden');

            try {
                const response = await fetch('/api/unidades-medida', {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel carregar as unidades de medida.'));
                }

                unidadesMedida = data.items || [];
                applyUnidadeFilter();
                fillUnidadeSelect(unidadesMedida);
                loadedUnidadesOnce = true;
            } catch (error) {
                unidadeStatus.textContent = error.message;
                unidadeStatus.classList.remove('is-hidden');
            }
        }

        async function loadNextItemCodigo() {
            const response = await fetch('/api/itens/next-codigo', {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                },
            });
            const data = await parseApiResponse(response);

            if (!response.ok) {
                throw new Error(getApiErrorMessage(data, 'Nao foi possivel gerar o codigo do item.'));
            }

            itemCodigoInput.value = data.codigo || '';
        }

        async function loadNextCotacaoNumero() {
            const response = await fetch('/api/cotacoes/next-numero', {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                },
            });
            const data = await parseApiResponse(response);

            if (!response.ok) {
                throw new Error(getApiErrorMessage(data, 'Nao foi possivel gerar o numero da cotacao.'));
            }

            cotacaoNumeroInput.value = data.numero || '';
        }

        async function loadCotacaoContexto() {
            cotacaoListStatus.classList.add('is-hidden');

            try {
                const response = await fetch('/api/cotacoes/contexto', {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel carregar dados da cotacao.'));
                }

                cotacaoClientes = data.clientes || [];
                cotacaoItensDisponiveis = data.itens || [];
                fillCotacaoClientes(cotacaoClientes);
                fillCotacaoItens(cotacaoItensDisponiveis);
                loadedCotacaoContextoOnce = true;
            } catch (error) {
                cotacaoListStatus.textContent = error.message;
                cotacaoListStatus.classList.remove('is-hidden');
            }
        }

        async function loadCotacoes() {
            cotacaoListStatus.classList.add('is-hidden');

            try {
                const response = await fetch('/api/cotacoes', {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel carregar as cotacoes.'));
                }

                cotacaoItems = data.items || [];
                applyCotacaoFilter();
                loadedCotacoesOnce = true;
            } catch (error) {
                cotacaoListStatus.textContent = error.message;
                cotacaoListStatus.classList.remove('is-hidden');
            }
        }

        if (cfSearchInput) {
            cfSearchInput.addEventListener('input', () => {
                applyCfFilter();
            });
        }

        if (itemSearchInput) {
            itemSearchInput.addEventListener('input', () => {
                applyItemFilter();
            });
        }

        if (unidadeSearchInput) {
            unidadeSearchInput.addEventListener('input', () => {
                applyUnidadeFilter();
            });
        }

        if (cotacaoSearchInput) {
            cotacaoSearchInput.addEventListener('input', () => {
                applyCotacaoFilter();
            });
        }

        async function resetCotacaoForm() {
            cotacaoForm.reset();
            cotacaoCreateStatus.classList.add('is-hidden');
            cotacaoItensSelecionados = [];
            cotacaoSelectedItemId = null;
            cotacaoItemSelect.value = '';
            cotacaoItemSuggestions.innerHTML = '';
            cotacaoItemSuggestions.classList.add('is-hidden');
            cotacaoItemQuantidadeInput.value = 1;
            renderCotacaoItensSelecionados();
            cotacaoDataInput.value = new Date().toISOString().slice(0, 10);
            await loadNextCotacaoNumero();
            fillCotacaoItens(cotacaoItensDisponiveis);
        }

        links.forEach((link) => {
            link.addEventListener('click', () => {
                const target = link.dataset.target;

                links.forEach((item) => item.classList.remove('active'));
                link.classList.add('active');

                panels.forEach((panel) => {
                    panel.classList.remove('active');
                });

                const selectedPanel = document.getElementById(`panel-${target}`);
                if (selectedPanel) {
                    selectedPanel.classList.add('active');
                    emptyPanel.classList.add('is-hidden');
                    cotacaoNavBtn.classList.remove('active');

                    if (target === 'clientes-fornecedores' && !loadedCfOnce) {
                        loadClientesFornecedores();
                    }

                    if (target === 'itens' && !loadedItemsOnce) {
                        loadItens();
                    }

                    if (target === 'itens' && !loadedUnidadesOnce) {
                        loadUnidadesMedida();
                    }

                }

                cadastroMenu.classList.add('is-hidden');
            });
        });

        document.addEventListener('click', (event) => {
            if (!event.target.closest('.cadastro-dropdown')) {
                cadastroMenu.classList.add('is-hidden');
            }
        });

        cotacaoNavBtn.addEventListener('click', async () => {
            links.forEach((item) => item.classList.remove('active'));
            panels.forEach((panel) => panel.classList.remove('active'));

            const cotacaoPanel = document.getElementById('panel-cotacao');
            if (cotacaoPanel) {
                cotacaoPanel.classList.add('active');
                emptyPanel.classList.add('is-hidden');
            }

            cadastroMenu.classList.add('is-hidden');
            cotacaoNavBtn.classList.add('active');

            if (!loadedCotacaoContextoOnce) {
                await loadCotacaoContexto();
            }

            if (!loadedCotacoesOnce) {
                await loadCotacoes();
            }
        });

        modalOpenButtons.forEach((button) => {
            button.addEventListener('click', () => {
                editingId = null;
                modalTitle.textContent = 'Novo Cadastro de Cliente e Fornecedor';
                saveCadastroBtn.textContent = 'Salvar';
                cadastroForm.reset();
                cadastroModalStatus.classList.add('is-hidden');
                modal.classList.remove('is-hidden');
                modal.setAttribute('aria-hidden', 'false');
            });
        });

        itemOpenButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                cadastroMenu.classList.add('is-hidden');
                itemStatus.classList.add('is-hidden');
                itemModalStatus.classList.add('is-hidden');
                editingItemId = null;
                itemForm.reset();
                itemModalTitle.textContent = 'Novo Item';
                saveItemBtn.textContent = 'Salvar';
                saveItemBtn.disabled = true;
                itemCodigoInput.value = 'Gerando...';

                itemModal.classList.remove('is-hidden');
                itemModal.setAttribute('aria-hidden', 'false');

                try {
                    if (!loadedUnidadesOnce) {
                        await loadUnidadesMedida();
                    }

                    if (!unidadesMedida.length) {
                        throw new Error('Cadastre ao menos uma unidade de medida antes de criar o item.');
                    }

                    await loadNextItemCodigo();
                } catch (error) {
                    itemModalStatus.textContent = error.message;
                    itemModalStatus.classList.remove('is-hidden');
                } finally {
                    saveItemBtn.disabled = false;
                }
            });
        });

        unidadeOpenButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                cadastroMenu.classList.add('is-hidden');
                unidadeStatus.classList.add('is-hidden');
                unidadeForm.reset();
                editingUnidadeId = null;
                saveUnidadeBtn.textContent = 'Salvar';
                unidadeModal.classList.remove('is-hidden');
                unidadeModal.setAttribute('aria-hidden', 'false');
                await loadUnidadesMedida();
            });
        });

        modalCloseButtons.forEach((button) => {
            button.addEventListener('click', () => {
                closeModal();
            });
        });

        itemCloseButtons.forEach((button) => {
            button.addEventListener('click', () => {
                closeItemModal();
            });
        });

        unidadeCloseButtons.forEach((button) => {
            button.addEventListener('click', () => {
                closeUnidadeModal();
            });
        });

        confirmCloseButtons.forEach((button) => {
            button.addEventListener('click', () => {
                closeConfirmModal(false);
            });
        });

        confirmCancelBtn.addEventListener('click', () => {
            closeConfirmModal(false);
        });

        confirmOkBtn.addEventListener('click', () => {
            closeConfirmModal(true);
        });

        cotacaoCreateCloseButtons.forEach((button) => {
            button.addEventListener('click', () => {
                closeCotacaoCreateModal();
            });
        });

        cotacaoDetailCloseButtons.forEach((button) => {
            button.addEventListener('click', () => {
                closeCotacaoDetailModal();
            });
        });

        novaCotacaoBtn.addEventListener('click', async () => {
            cotacaoCreateStatus.classList.add('is-hidden');

            if (!loadedCotacaoContextoOnce) {
                await loadCotacaoContexto();
            }

            await resetCotacaoForm();
            cotacaoCreateModal.classList.remove('is-hidden');
            cotacaoCreateModal.setAttribute('aria-hidden', 'false');
        });

        cotacaoAddItemBtn.addEventListener('click', () => {
            cotacaoCreateStatus.classList.add('is-hidden');

            const itemTexto = (cotacaoItemSelect.value || '').trim();
            const quantidade = Number(cotacaoItemQuantidadeInput.value || 0);

            if (!itemTexto) {
                cotacaoCreateStatus.textContent = 'Digite ou selecione um item para adicionar.';
                cotacaoCreateStatus.classList.remove('is-hidden');
                return;
            }

            if (!Number.isInteger(quantidade) || quantidade < 1) {
                cotacaoCreateStatus.textContent = 'Informe uma quantidade valida.';
                cotacaoCreateStatus.classList.remove('is-hidden');
                return;
            }

            let itemId = Number(cotacaoSelectedItemId || 0);
            if (!itemId) {
                const exactMatch = cotacaoItensDisponiveis.find((item) => {
                    return getCotacaoItemLabel(item).toLowerCase() === itemTexto.toLowerCase();
                });
                if (exactMatch) {
                    itemId = Number(exactMatch.id);
                }
            }

            if (!itemId) {
                const termo = itemTexto.toLowerCase();
                const matches = cotacaoItensDisponiveis.filter((item) => {
                    const codigo = String(item.codigo || '').toLowerCase();
                    const descricao = String(item.descricao || '').toLowerCase();
                    const unidade = String(item.unidade_medida || '').toLowerCase();
                    return codigo.includes(termo) || descricao.includes(termo) || unidade.includes(termo);
                });

                if (matches.length === 1) {
                    itemId = Number(matches[0].id);
                } else {
                    cotacaoCreateStatus.textContent = 'Selecione um item valido da lista de sugestoes.';
                    cotacaoCreateStatus.classList.remove('is-hidden');
                    return;
                }
            }

            const itemBase = cotacaoItensDisponiveis.find((item) => Number(item.id) === Number(itemId));
            if (!itemBase) {
                cotacaoCreateStatus.textContent = 'Item selecionado nao encontrado.';
                cotacaoCreateStatus.classList.remove('is-hidden');
                return;
            }

            const existente = cotacaoItensSelecionados.find((item) => Number(item.id) === itemId);
            if (existente) {
                existente.quantidade += quantidade;
            } else {
                cotacaoItensSelecionados.push({
                    id: itemBase.id,
                    codigo: itemBase.codigo,
                    descricao: itemBase.descricao,
                    unidade_medida: itemBase.unidade_medida,
                    preco: Number(itemBase.preco || 0),
                    quantidade,
                });
            }

            cotacaoSelectedItemId = null;
            cotacaoItemSelect.value = '';
            cotacaoItemSuggestions.innerHTML = '';
            cotacaoItemSuggestions.classList.add('is-hidden');
            cotacaoSuggestionIndex = -1;
            cotacaoItemQuantidadeInput.value = 1;
            renderCotacaoItensSelecionados();
        });

        cotacaoItemSelect.addEventListener('keydown', (event) => {
            const buttons = getCotacaoSuggestionButtons();
            const hasSuggestions = !cotacaoItemSuggestions.classList.contains('is-hidden') && buttons.length > 0;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                if (!hasSuggestions) {
                    renderCotacaoItemSuggestions(cotacaoItemSelect.value, true);
                }

                const currentButtons = getCotacaoSuggestionButtons();
                if (!currentButtons.length) {
                    return;
                }

                cotacaoSuggestionIndex = (cotacaoSuggestionIndex + 1 + currentButtons.length) % currentButtons.length;
                updateCotacaoSuggestionHighlight();
                return;
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                if (!hasSuggestions) {
                    renderCotacaoItemSuggestions(cotacaoItemSelect.value, true);
                }

                const currentButtons = getCotacaoSuggestionButtons();
                if (!currentButtons.length) {
                    return;
                }

                cotacaoSuggestionIndex = cotacaoSuggestionIndex <= 0
                    ? currentButtons.length - 1
                    : cotacaoSuggestionIndex - 1;
                updateCotacaoSuggestionHighlight();
                return;
            }

            if (event.key === 'Escape') {
                cotacaoSuggestionIndex = -1;
                cotacaoItemSuggestions.classList.add('is-hidden');
                return;
            }

            if (event.key === 'Enter') {
                event.preventDefault();

                const currentButtons = getCotacaoSuggestionButtons();
                if (!cotacaoItemSuggestions.classList.contains('is-hidden') && cotacaoSuggestionIndex >= 0 && currentButtons[cotacaoSuggestionIndex]) {
                    applyCotacaoSuggestionSelection(currentButtons[cotacaoSuggestionIndex]);
                    return;
                }

                cotacaoAddItemBtn.click();
            }
        });

        cotacaoItemSelect.addEventListener('input', () => {
            cotacaoSelectedItemId = null;
            renderCotacaoItemSuggestions(cotacaoItemSelect.value, true);
        });

        cotacaoItemSelect.addEventListener('click', () => {
            renderCotacaoItemSuggestions(cotacaoItemSelect.value, true);
        });

        cotacaoItemSuggestions.addEventListener('click', (event) => {
            const option = event.target.closest('.autocomplete-option');
            if (!option) {
                return;
            }

            applyCotacaoSuggestionSelection(option);
        });

        document.addEventListener('click', (event) => {
            if (event.target.closest('.cotacao-item-field')) {
                return;
            }

            cotacaoSuggestionIndex = -1;
            cotacaoItemSuggestions.classList.add('is-hidden');
        });

        cotacaoItensBody.addEventListener('click', (event) => {
            const removeButton = event.target.closest('.cotacao-remove-item-btn');
            if (!removeButton) {
                return;
            }

            const itemId = Number(removeButton.dataset.id);
            cotacaoItensSelecionados = cotacaoItensSelecionados.filter((item) => Number(item.id) !== itemId);
            renderCotacaoItensSelecionados();
        });

        cotacaoListBody.addEventListener('click', async (event) => {
            const viewButton = event.target.closest('.cotacao-view-btn');
            const deleteButton = event.target.closest('.cotacao-delete-btn');

            if (!viewButton && !deleteButton) {
                return;
            }

            const button = viewButton || deleteButton;
            const cotacaoId = Number(button.dataset.id);
            if (!cotacaoId) {
                return;
            }

            if (deleteButton) {
                const confirmar = await openConfirmModal('Confirma excluir esta cotacao?');
                if (!confirmar) {
                    return;
                }

                cotacaoListStatus.classList.add('is-hidden');

                try {
                    const response = await fetch(`/api/cotacoes/${cotacaoId}`, {
                        method: 'DELETE',
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });
                    const data = await parseApiResponse(response);

                    if (!response.ok) {
                        throw new Error(getApiErrorMessage(data, 'Nao foi possivel excluir a cotacao.'));
                    }

                    cotacaoListStatus.textContent = data.message || 'Cotacao excluida com sucesso.';
                    cotacaoListStatus.classList.remove('is-hidden');
                    await loadCotacoes();
                } catch (error) {
                    cotacaoListStatus.textContent = error.message;
                    cotacaoListStatus.classList.remove('is-hidden');
                }
                return;
            }

            cotacaoDetailStatus.classList.add('is-hidden');
            cotacaoDetailItensBody.innerHTML = '<tr><td colspan="6">Carregando...</td></tr>';
            cotacaoDetailModal.classList.remove('is-hidden');
            cotacaoDetailModal.setAttribute('aria-hidden', 'false');

            try {
                const response = await fetch(`/api/cotacoes/${cotacaoId}`, {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel carregar os detalhes da cotacao.'));
                }

                renderCotacaoDetail(data.cotacao || null, data.itens || []);
            } catch (error) {
                cotacaoDetailStatus.textContent = error.message;
                cotacaoDetailStatus.classList.remove('is-hidden');
                cotacaoDetailItensBody.innerHTML = '<tr><td colspan="6">Nenhum item encontrado.</td></tr>';
            }
        });

        cotacaoLimparBtn.addEventListener('click', async () => {
            await resetCotacaoForm();
        });

        cotacaoSalvarBtn.addEventListener('click', async () => {
            cotacaoCreateStatus.classList.add('is-hidden');
            cotacaoSalvarBtn.disabled = true;
            cotacaoSalvarBtn.textContent = 'Salvando...';

            const clienteId = Number(cotacaoClienteSelect.value || 0);
            const numero = (cotacaoNumeroInput.value || '').trim();
            const dataEmissao = cotacaoDataInput.value;
            const observacoes = (cotacaoObservacoesInput.value || '').trim();

            if (!clienteId) {
                cotacaoCreateStatus.textContent = 'Selecione o cliente da cotacao.';
                cotacaoCreateStatus.classList.remove('is-hidden');
                cotacaoSalvarBtn.disabled = false;
                cotacaoSalvarBtn.textContent = 'Salvar Cotacao';
                return;
            }

            if (!numero) {
                cotacaoCreateStatus.textContent = 'Numero da cotacao nao foi gerado.';
                cotacaoCreateStatus.classList.remove('is-hidden');
                cotacaoSalvarBtn.disabled = false;
                cotacaoSalvarBtn.textContent = 'Salvar Cotacao';
                return;
            }

            if (!dataEmissao) {
                cotacaoCreateStatus.textContent = 'Informe a data da cotacao.';
                cotacaoCreateStatus.classList.remove('is-hidden');
                cotacaoSalvarBtn.disabled = false;
                cotacaoSalvarBtn.textContent = 'Salvar Cotacao';
                return;
            }

            if (!cotacaoItensSelecionados.length) {
                cotacaoCreateStatus.textContent = 'Adicione ao menos um item na cotacao.';
                cotacaoCreateStatus.classList.remove('is-hidden');
                cotacaoSalvarBtn.disabled = false;
                cotacaoSalvarBtn.textContent = 'Salvar Cotacao';
                return;
            }

            const payload = {
                numero,
                cliente_fornecedor_id: clienteId,
                data_emissao: dataEmissao,
                observacoes,
                itens: cotacaoItensSelecionados.map((item) => ({
                    item_id: Number(item.id),
                    quantidade: Number(item.quantidade),
                })),
            };

            try {
                const response = await fetch('/api/cotacoes', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel salvar a cotacao.'));
                }

                cotacaoListStatus.textContent = data.message || 'Cotacao salva com sucesso.';
                cotacaoListStatus.classList.remove('is-hidden');
                await loadCotacoes();
                await resetCotacaoForm();
                closeCotacaoCreateModal();
            } catch (error) {
                cotacaoCreateStatus.textContent = error.message;
                cotacaoCreateStatus.classList.remove('is-hidden');
            } finally {
                cotacaoSalvarBtn.disabled = false;
                cotacaoSalvarBtn.textContent = 'Salvar Cotacao';
            }
        });

        cadastroForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            cfStatus.classList.add('is-hidden');
            cadastroModalStatus.classList.add('is-hidden');
            saveCadastroBtn.disabled = true;
            saveCadastroBtn.textContent = editingId ? 'Atualizando...' : 'Salvando...';

            const payload = Object.fromEntries(new FormData(cadastroForm).entries());
            const isEditing = editingId !== null;
            const url = isEditing ? `/api/clientes-fornecedores/${editingId}` : '/api/clientes-fornecedores';
            const method = isEditing ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method,
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel salvar.'));
                }

                closeModal();

                cfStatus.textContent = data.message || (isEditing ? 'Cadastro atualizado com sucesso.' : 'Cadastro salvo com sucesso.');
                cfStatus.classList.remove('is-hidden');
                await loadClientesFornecedores();
            } catch (error) {
                cadastroModalStatus.textContent = error.message;
                cadastroModalStatus.classList.remove('is-hidden');
            } finally {
                saveCadastroBtn.disabled = false;
                saveCadastroBtn.textContent = editingId ? 'Atualizar' : 'Salvar';
            }
        });

        itemForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            itemStatus.classList.add('is-hidden');
            itemModalStatus.classList.add('is-hidden');
            saveItemBtn.disabled = true;
            saveItemBtn.textContent = editingItemId !== null ? 'Atualizando...' : 'Salvando...';

            const rawPayload = Object.fromEntries(new FormData(itemForm).entries());
            const payload = {
                codigo: (rawPayload.codigo || '').trim(),
                descricao: (rawPayload.descricao || '').trim(),
                unidade_medida: (rawPayload.unidade_medida || '').trim(),
                preco: Number(String(rawPayload.preco || '0').replace(',', '.')),
                estoque: Number(rawPayload.estoque || 0),
            };
            const isEditingItem = editingItemId !== null;
            const url = isEditingItem ? `/api/itens/${editingItemId}` : '/api/itens';
            const method = isEditingItem ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method,
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, isEditingItem ? 'Nao foi possivel atualizar o item.' : 'Nao foi possivel salvar o item.'));
                }

                closeItemModal();
                itemStatus.textContent = data.message || (isEditingItem ? 'Item atualizado com sucesso.' : 'Item salvo com sucesso.');
                itemStatus.classList.remove('is-hidden');
                await loadItens();
            } catch (error) {
                itemModalStatus.textContent = error.message;
                itemModalStatus.classList.remove('is-hidden');
            } finally {
                saveItemBtn.disabled = false;
                saveItemBtn.textContent = editingItemId !== null ? 'Atualizar' : 'Salvar';
            }
        });

        unidadeForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            unidadeStatus.classList.add('is-hidden');
            saveUnidadeBtn.disabled = true;
            saveUnidadeBtn.textContent = editingUnidadeId !== null ? 'Atualizando...' : 'Salvando...';

            const rawPayload = Object.fromEntries(new FormData(unidadeForm).entries());
            const payload = {
                sigla: (rawPayload.sigla || '').trim(),
                descricao: (rawPayload.descricao || '').trim(),
            };
            const isEditingUnidade = editingUnidadeId !== null;
            const url = isEditingUnidade ? `/api/unidades-medida/${editingUnidadeId}` : '/api/unidades-medida';
            const method = isEditingUnidade ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method,
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, isEditingUnidade ? 'Nao foi possivel atualizar a unidade de medida.' : 'Nao foi possivel salvar a unidade de medida.'));
                }

                editingUnidadeId = null;
                unidadeForm.reset();
                unidadeStatus.textContent = data.message || (isEditingUnidade ? 'Unidade de medida atualizada com sucesso.' : 'Unidade de medida salva com sucesso.');
                unidadeStatus.classList.remove('is-hidden');
                saveUnidadeBtn.textContent = 'Salvar';
                await loadUnidadesMedida();
            } catch (error) {
                unidadeStatus.textContent = error.message;
                unidadeStatus.classList.remove('is-hidden');
            } finally {
                saveUnidadeBtn.disabled = false;
                saveUnidadeBtn.textContent = editingUnidadeId !== null ? 'Atualizar' : 'Salvar';
            }
        });

        unidadeTableBody.addEventListener('click', async (event) => {
            const editButton = event.target.closest('.unidade-edit-btn');
            const deleteButton = event.target.closest('.unidade-delete-btn');

            if (!editButton && !deleteButton) {
                return;
            }

            const button = editButton || deleteButton;
            const id = Number(button.dataset.id);
            const unidade = unidadesMedida.find((item) => Number(item.id) === id);

            if (!unidade) {
                return;
            }

            const emUso = Boolean(Number(unidade.em_uso) || unidade.em_uso === true);
            if (emUso) {
                unidadeStatus.textContent = 'Unidade em uso por item cadastrado. Nao e permitido editar ou excluir.';
                unidadeStatus.classList.remove('is-hidden');
                return;
            }

            unidadeStatus.classList.add('is-hidden');

            if (editButton) {
                editingUnidadeId = unidade.id;
                unidadeForm.elements.sigla.value = unidade.sigla || '';
                unidadeForm.elements.descricao.value = unidade.descricao || '';
                saveUnidadeBtn.textContent = 'Atualizar';
                return;
            }

            const confirmar = await openConfirmModal(`Confirma excluir a unidade "${unidade.sigla}"?`);
            if (!confirmar) {
                return;
            }

            try {
                const response = await fetch(`/api/unidades-medida/${unidade.id}`, {
                    method: 'DELETE',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel excluir a unidade de medida.'));
                }

                if (editingUnidadeId === unidade.id) {
                    editingUnidadeId = null;
                    unidadeForm.reset();
                    saveUnidadeBtn.textContent = 'Salvar';
                }

                unidadeStatus.textContent = data.message || 'Unidade de medida excluida com sucesso.';
                unidadeStatus.classList.remove('is-hidden');
                await loadUnidadesMedida();
            } catch (error) {
                unidadeStatus.textContent = error.message;
                unidadeStatus.classList.remove('is-hidden');
            }
        });

        itemTableBody.addEventListener('click', async (event) => {
            const editButton = event.target.closest('.item-edit-btn');
            const deleteButton = event.target.closest('.item-delete-btn');

            if (!editButton && !deleteButton) {
                return;
            }

            const button = editButton || deleteButton;
            const id = Number(button.dataset.id);
            const item = itemItems.find((entry) => Number(entry.id) === id);

            if (!item) {
                return;
            }

            const emUso = Boolean(Number(item.em_uso) || item.em_uso === true);
            if (emUso) {
                itemStatus.textContent = 'Item em uso em outros registros. Nao e permitido editar ou excluir.';
                itemStatus.classList.remove('is-hidden');
                return;
            }

            itemStatus.classList.add('is-hidden');

            if (editButton) {
                if (!loadedUnidadesOnce) {
                    await loadUnidadesMedida();
                }

                editingItemId = item.id;
                itemModalTitle.textContent = 'Editar Item';
                saveItemBtn.textContent = 'Atualizar';
                itemModalStatus.classList.add('is-hidden');
                itemForm.elements.codigo.value = item.codigo || '';
                itemForm.elements.descricao.value = item.descricao || '';
                itemForm.elements.unidade_medida.value = item.unidade_medida || '';
                itemForm.elements.preco.value = item.preco ?? 0;
                itemForm.elements.estoque.value = item.estoque ?? 0;
                itemModal.classList.remove('is-hidden');
                itemModal.setAttribute('aria-hidden', 'false');
                return;
            }

            const confirmar = await openConfirmModal(`Confirma excluir o item "${item.codigo} - ${item.descricao}"?`);
            if (!confirmar) {
                return;
            }

            try {
                const response = await fetch(`/api/itens/${item.id}`, {
                    method: 'DELETE',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel excluir o item.'));
                }

                itemStatus.textContent = data.message || 'Item excluido com sucesso.';
                itemStatus.classList.remove('is-hidden');
                await loadItens();
            } catch (error) {
                itemStatus.textContent = error.message;
                itemStatus.classList.remove('is-hidden');
            }
        });

        cfTableBody.addEventListener('click', async (event) => {
            const editButton = event.target.closest('.row-edit-btn');
            const deleteButton = event.target.closest('.cf-delete-btn');

            if (!editButton && !deleteButton) {
                return;
            }

            const button = editButton || deleteButton;
            const id = Number(button.dataset.id);
            const item = cfItems.find((entry) => Number(entry.id) === id);

            if (!item) {
                return;
            }

            if (editButton) {
                editingId = item.id;
                modalTitle.textContent = 'Editar Cadastro de Cliente e Fornecedor';
                saveCadastroBtn.textContent = 'Atualizar';
                cadastroModalStatus.classList.add('is-hidden');

                cadastroForm.elements.nome_razao_social.value = item.nome_razao_social || '';
                cadastroForm.elements.nome_fantasia.value = item.nome_fantasia || '';
                cadastroForm.elements.cnpj_cpf.value = item.cnpj_cpf || '';
                cadastroForm.elements.ie_rg.value = item.ie_rg || '';
                cadastroForm.elements.telefone_contato.value = item.telefone_contato || '';
                cadastroForm.elements.email_contato.value = item.email_contato || '';
                cadastroForm.elements.cep.value = item.cep || '';
                cadastroForm.elements.endereco.value = item.endereco || '';
                cadastroForm.elements.numero.value = item.numero || '';
                cadastroForm.elements.complemento.value = item.complemento || '';
                cadastroForm.elements.bairro.value = item.bairro || '';
                cadastroForm.elements.cidade.value = item.cidade || '';
                cadastroForm.elements.uf.value = item.uf || '';

                modal.classList.remove('is-hidden');
                modal.setAttribute('aria-hidden', 'false');
                return;
            }

            const nome = item.nome_razao_social || 'este cadastro';
            const confirmar = await openConfirmModal(`Confirma excluir "${nome}"?`);
            if (!confirmar) {
                return;
            }

            cfStatus.classList.add('is-hidden');

            try {
                const response = await fetch(`/api/clientes-fornecedores/${item.id}`, {
                    method: 'DELETE',
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });
                const data = await parseApiResponse(response);

                if (!response.ok) {
                    throw new Error(getApiErrorMessage(data, 'Nao foi possivel excluir o cadastro.'));
                }

                cfStatus.textContent = data.message || 'Cadastro excluido com sucesso.';
                cfStatus.classList.remove('is-hidden');
                await loadClientesFornecedores();
            } catch (error) {
                cfStatus.textContent = error.message;
                cfStatus.classList.remove('is-hidden');
            }
        });

        if (cepInput) {
            cepInput.addEventListener('blur', async () => {
                const cep = cepInput.value.replace(/\D/g, '');

                if (cep.length !== 8) {
                    return;
                }

                try {
                    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                    const data = await response.json();

                    if (data.erro) {
                        return;
                    }

                    enderecoInput.value = data.logradouro || '';
                    bairroInput.value = data.bairro || '';
                    cidadeInput.value = data.localidade || '';
                    ufSelect.value = data.uf || '';
                } catch (_) {
                    // Se falhar, o preenchimento continua manual.
                }
            });
        }
    </script>
</body>
</html>
