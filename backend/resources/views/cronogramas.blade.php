
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cronograma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/dashboard-cronograma.css">
</head>
<body>
    <main class="cronograma-page">
        <header class="topbar">
            <div class="topbar-content">
                <div>
                    <p class="tag">Sistema selecionado</p>
                    <h1>Cronograma</h1>
                </div>

                <div class="topbar-actions">
                    <form method="GET" action="/selecionar-ambiente">
                        <button type="submit" class="ghost-btn">Trocar ambiente</button>
                    </form>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit">Sair</button>
                    </form>
                </div>
            </div>
        </header>

        <section class="content">
            <div class="content-head">
                <div>
                    <h2>Planejamento</h2>
                    <p>Edite as linhas na tabela e acompanhe as barras do cronograma ao lado.</p>
                </div>
                <div class="head-actions">
                    <div class="timeline-scale" aria-label="Escala de visualizacao">
                        <button type="button" data-scale="dias" class="is-active">Dias</button>
                        <button type="button" data-scale="semanas">Semanas</button>
                        <button type="button" data-scale="meses">Meses</button>
                    </div>
                    <div class="cadastro-menu-wrap">
                        <button type="button" id="open-cadastros-btn">Cadastros</button>
                        <div id="cadastro-menu" class="cadastro-menu is-hidden">
                            <button type="button" data-open-cadastro-type="etapas">Etapa</button>
                            <button type="button" data-open-cadastro-type="setores">Setor</button>
                            <button type="button" data-open-cadastro-type="tarefas">Tarefa</button>
                            <button type="button" data-open-cadastro-type="subtarefas">Subtarefa</button>
                        </div>
                    </div>
                    <button type="button" id="add-task-btn">Nova Linha</button>
                </div>
            </div>

            <div class="planner-shell">
                <section class="sheet-panel">
                    <div class="sheet-scroll" id="sheet-scroll">
                        <table class="sheet-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Tarefa</th>
                                    <th>Subtarefa</th>
                                    <th>Duracao</th>
                                    <th>Inicio</th>
                                    <th>Termino</th>
                                    <th>Interv.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="task-table-body"></tbody>
                        </table>
                    </div>
                </section>

                <section class="timeline-panel">
                    <div class="timeline-header-scroll" id="timeline-header-scroll">
                        <div id="timeline-header"></div>
                    </div>
                    <div class="timeline-body-scroll" id="timeline-body-scroll">
                        <div id="timeline-rows"></div>
                    </div>
                </section>
            </div>
        </section>
    </main>

    <div id="cadastros-modal" class="modal is-hidden" aria-hidden="true">
        <div class="modal-backdrop" data-action="close-cadastros"></div>
        <section class="modal-card" role="dialog" aria-modal="true" aria-labelledby="cadastro-active-title">
            <header class="modal-header">
                <h3 id="cadastro-active-title">Etapas</h3>
                <button type="button" class="modal-close" data-action="close-cadastros" aria-label="Fechar">x</button>
            </header>
            <article class="cadastro-box cadastro-box-inline">
                <form id="cadastro-active-form" class="cadastro-form">
                    <input type="text" id="cadastro-active-input" placeholder="Nova etapa" required>
                    <button type="submit">Adicionar</button>
                </form>
                <ul id="cadastro-active-list" class="cadastro-list"></ul>
            </article>
        </section>
    </div>

    <div id="row-edit-modal" class="modal is-hidden" aria-hidden="true">
        <div class="modal-backdrop" data-action="close-row-edit"></div>
        <section class="modal-card context-modal-card" role="dialog" aria-modal="true" aria-labelledby="row-edit-title">
            <header class="modal-header">
                <h3 id="row-edit-title">Editar Etapa e Setor da Linha</h3>
                <button type="button" class="modal-close" data-action="close-row-edit" aria-label="Fechar">x</button>
            </header>
            <form id="row-edit-form" class="context-form">
                <label>Etapa<select id="row-edit-etapa" required></select></label>
                <label>Setor<select id="row-edit-setor" required></select></label>
                <div class="context-actions">
                    <button type="button" data-action="close-row-edit">Cancelar</button>
                    <button type="submit">Salvar</button>
                </div>
            </form>
        </section>
    </div>

    <div id="new-row-modal" class="modal is-hidden" aria-hidden="true">
        <div class="modal-backdrop" data-action="close-new-row"></div>
        <section class="modal-card context-modal-card" role="dialog" aria-modal="true" aria-labelledby="new-row-title">
            <header class="modal-header">
                <h3 id="new-row-title">Nova Linha</h3>
                <button type="button" class="modal-close" data-action="close-new-row" aria-label="Fechar">x</button>
            </header>
            <form id="new-row-form" class="context-form">
                <label>Etapa<select id="new-row-etapa" required></select></label>
                <label>Setor<select id="new-row-setor" required></select></label>
                <div class="context-actions">
                    <button type="button" data-action="close-new-row">Cancelar</button>
                    <button type="submit">Inserir</button>
                </div>
            </form>
        </section>
    </div>

    <script>
        const MIN_DAYS = 10;
        const STORAGE_KEY = 'cronograma_tasks_v1';
        const STORAGE_CATALOGS_KEY = 'cronograma_catalogs_v1';
        const today = new Date();
        const weekDayNames = ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sab'];
        const cadastroLabels = { etapas: 'Etapas', setores: 'Setores', tarefas: 'Tarefas', subtarefas: 'Subtarefas' };
        const cadastroPlaceholders = { etapas: 'Nova etapa', setores: 'Novo setor', tarefas: 'Nova tarefa', subtarefas: 'Nova subtarefa' };

        const defaultCatalogs = {
            etapas: ['Unica'],
            setores: ['Comercial', 'Engenharia'],
            tarefas: ['Proposta consolidada', 'Aviso de obra contratada', 'Projeto executivo'],
            subtarefas: ['Envio', 'Assinatura', 'Elaboracao'],
        };

        const addTaskBtn = document.getElementById('add-task-btn');
        const openCadastrosBtn = document.getElementById('open-cadastros-btn');
        const scaleButtons = Array.from(document.querySelectorAll('.timeline-scale button[data-scale]'));
        const cadastroMenu = document.getElementById('cadastro-menu');
        const cadastrosModal = document.getElementById('cadastros-modal');
        const rowEditModal = document.getElementById('row-edit-modal');
        const newRowModal = document.getElementById('new-row-modal');
        const rowEditForm = document.getElementById('row-edit-form');
        const rowEditEtapaSelect = document.getElementById('row-edit-etapa');
        const rowEditSetorSelect = document.getElementById('row-edit-setor');
        const newRowForm = document.getElementById('new-row-form');
        const newRowEtapaSelect = document.getElementById('new-row-etapa');
        const newRowSetorSelect = document.getElementById('new-row-setor');
        const cadastroActiveTitle = document.getElementById('cadastro-active-title');
        const cadastroActiveForm = document.getElementById('cadastro-active-form');
        const cadastroActiveInput = document.getElementById('cadastro-active-input');
        const cadastroActiveList = document.getElementById('cadastro-active-list');
        const tableBody = document.getElementById('task-table-body');
        const timelineHeader = document.getElementById('timeline-header');
        const timelineRows = document.getElementById('timeline-rows');
        const sheetScroll = document.getElementById('sheet-scroll');
        const timelineHeaderScroll = document.getElementById('timeline-header-scroll');
        const timelineBodyScroll = document.getElementById('timeline-body-scroll');

        let catalogs = { ...defaultCatalogs };
        let tasks = [];
        const collapsedEtapas = new Set();
        const collapsedSetores = new Set();
        let activeCadastroType = 'etapas';
        let activeScale = 'dias';
        let editingRowId = null;
        let linkSourceTaskId = null;
        let reorderTaskId = null;
        let hoveredSourceCell = null;

        const pad = (n) => String(n).padStart(2, '0');
        const normalizeCatalogValue = (v) => String(v || '').trim();
        const sortAlpha = (list) => [...(list || [])].sort((a, b) => String(a || '').localeCompare(String(b || ''), 'pt-BR', { sensitivity: 'base' }));
        const addDays = (date, amount) => { const d = new Date(date); d.setDate(d.getDate() + amount); return d; };
        const endOfMonth = (date) => new Date(date.getFullYear(), date.getMonth() + 1, 0);
        const diffDays = (a, b) => Math.round((new Date(b.getFullYear(), b.getMonth(), b.getDate()) - new Date(a.getFullYear(), a.getMonth(), a.getDate())) / 86400000);
        const escapeAttr = (value) => String(value ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

        const parseDateInput = (value) => {
            if (!value || typeof value !== 'string') return new Date(NaN);
            const iso = value.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (iso) return new Date(Number(iso[1]), Number(iso[2]) - 1, Number(iso[3]));
            const br = value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            if (br) return new Date(Number(br[3]), Number(br[2]) - 1, Number(br[1]));
            return new Date(NaN);
        };
        const toDateInput = (date) => { const d = new Date(date); return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`; };
        const toDateBr = (value) => { const d = typeof value === 'string' ? parseDateInput(value) : new Date(value); return Number.isNaN(d.getTime()) ? '' : `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`; };
        const getWeekDay = (value) => { const d = parseDateInput(value); return Number.isNaN(d.getTime()) ? '-' : weekDayNames[d.getDay()]; };

        const ensureCatalogValue = (type, value) => {
            const normalized = normalizeCatalogValue(value);
            if (!normalized) return;
            if (!catalogs[type]) catalogs[type] = [];
            if (!catalogs[type].includes(normalized)) catalogs[type].push(normalized);
        };

        const normalizeTaskDates = (task) => {
            if (Number.isNaN(parseDateInput(task.inicio).getTime())) task.inicio = toDateInput(today);
            task.duracao = Math.max(0, Math.round(Number(task.duracao) || 0));
            task.fim = toDateInput(addDays(parseDateInput(task.inicio), task.duracao));
        };

        const createTask = (etapaValue, setorValue) => {
            const etapa = normalizeCatalogValue(etapaValue) || (catalogs.etapas?.[0] || 'Unica');
            const setor = normalizeCatalogValue(setorValue) || (catalogs.setores?.[0] || '');
            const baseDate = toDateInput(today);
            ensureCatalogValue('etapas', etapa);
            ensureCatalogValue('setores', setor);
            return { id: Date.now() + Math.floor(Math.random() * 1000), etapa, setor, tarefa: sortAlpha(catalogs.tarefas)[0] || '', subtarefa: sortAlpha(catalogs.subtarefas)[0] || '', inicio: baseDate, duracao: 0, fim: baseDate, dependenciaTaskId: null, dependenciaTipo: null, intervalo: 0 };
        };

        const saveCatalogs = () => { try { localStorage.setItem(STORAGE_CATALOGS_KEY, JSON.stringify(catalogs)); } catch (_) {} };
        const saveTasks = () => { try { localStorage.setItem(STORAGE_KEY, JSON.stringify(tasks)); } catch (_) {} };

        const loadCatalogs = () => {
            try {
                const raw = localStorage.getItem(STORAGE_CATALOGS_KEY);
                if (!raw) return;
                const parsed = JSON.parse(raw);
                if (!parsed || typeof parsed !== 'object') return;
                ['etapas', 'setores', 'tarefas', 'subtarefas'].forEach((key) => {
                    const source = Array.isArray(parsed[key]) ? parsed[key] : defaultCatalogs[key];
                    catalogs[key] = [...new Set(source.map((item) => normalizeCatalogValue(item)).filter(Boolean))];
                    if (!catalogs[key].length) catalogs[key] = [...defaultCatalogs[key]];
                });
            } catch (_) {}
        };

        const loadTasks = () => {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (raw) {
                    const parsed = JSON.parse(raw);
                    if (Array.isArray(parsed) && parsed.length) {
                        tasks = parsed.map((task) => ({
                            id: Number(task.id) || Date.now(),
                            etapa: task.etapa || catalogs.etapas?.[0] || 'Unica',
                            setor: task.setor || catalogs.setores?.[0] || '',
                            tarefa: task.tarefa || catalogs.tarefas?.[0] || '',
                            subtarefa: task.subtarefa || catalogs.subtarefas?.[0] || '',
                            inicio: task.inicio || toDateInput(today),
                            duracao: Math.max(0, Math.round(Number(task.duracao) || 0)),
                            fim: task.fim || task.inicio || toDateInput(today),
                            dependenciaTaskId: task.dependenciaTaskId ?? null,
                            dependenciaTipo: task.dependenciaTipo ?? null,
                            intervalo: Math.round(Number(task.intervalo) || 0),
                        }));
                    }
                }
            } catch (_) {}
            if (!tasks.length) tasks = [createTask(catalogs.etapas?.[0], catalogs.setores?.[0])];
            tasks.forEach((task) => {
                ensureCatalogValue('etapas', task.etapa);
                ensureCatalogValue('setores', task.setor);
                ensureCatalogValue('tarefas', task.tarefa);
                ensureCatalogValue('subtarefas', task.subtarefa);
            });
        };

        const syncDependentDates = () => {
            const taskById = new Map(tasks.map((task) => [Number(task.id), task]));
            const resolving = new Set();
            const resolveTask = (task) => {
                const key = Number(task.id);
                if (resolving.has(key)) return;
                resolving.add(key);
                const hasDep = task.dependenciaTaskId !== null && task.dependenciaTaskId !== undefined;
                if (hasDep) {
                    const predecessor = taskById.get(Number(task.dependenciaTaskId));
                    if (predecessor && predecessor.id !== task.id) {
                        resolveTask(predecessor);
                        normalizeTaskDates(predecessor);
                        const baseDate = task.dependenciaTipo === 'inicio' ? parseDateInput(predecessor.inicio) : parseDateInput(predecessor.fim);
                        const lag = Math.round(Number(task.intervalo) || 0);
                        if (!Number.isNaN(baseDate.getTime())) task.inicio = toDateInput(addDays(baseDate, lag));
                    }
                }
                normalizeTaskDates(task);
                resolving.delete(key);
            };
            tasks.forEach((task) => resolveTask(task));
        };

        const getDependencyTooltip = (task) => {
            if (!task.dependenciaTaskId || !task.dependenciaTipo) return '';
            const predecessor = tasks.find((item) => Number(item.id) === Number(task.dependenciaTaskId));
            if (!predecessor) return '';
            const origem = task.dependenciaTipo === 'inicio' ? 'Inicio' : 'Termino';
            const lag = Math.round(Number(task.intervalo) || 0);
            return `Vinculo: ${origem} de ${predecessor.tarefa || `Tarefa #${predecessor.id}`}${predecessor.subtarefa ? ` - ${predecessor.subtarefa}` : ''} | Intervalo: ${lag} dia(s)`;
        };
        const moveTaskBefore = (sourceId, targetId) => {
            const sourceIndex = tasks.findIndex((task) => Number(task.id) === Number(sourceId));
            const targetIndex = tasks.findIndex((task) => Number(task.id) === Number(targetId));
            if (sourceIndex === -1 || targetIndex === -1 || sourceIndex === targetIndex) return false;
            const sourceTask = tasks[sourceIndex];
            const targetTask = tasks[targetIndex];
            if ((sourceTask.etapa || '') !== (targetTask.etapa || '') || (sourceTask.setor || '') !== (targetTask.setor || '')) return false;
            const [movedTask] = tasks.splice(sourceIndex, 1);
            const newTargetIndex = tasks.findIndex((task) => Number(task.id) === Number(targetId));
            if (newTargetIndex === -1) { tasks.push(movedTask); return true; }
            tasks.splice(newTargetIndex, 0, movedTask);
            return true;
        };

        const moveCatalogItem = (type, value, direction) => {
            if (!type || !value || !catalogs[type]) return false;
            const list = catalogs[type];
            const index = list.indexOf(value);
            if (index === -1) return false;
            const targetIndex = direction === 'up' ? index - 1 : index + 1;
            if (targetIndex < 0 || targetIndex >= list.length) return false;
            [list[index], list[targetIndex]] = [list[targetIndex], list[index]];
            saveCatalogs();
            return true;
        };

        const toSelectOptions = (type, selected) => {
            const normalized = normalizeCatalogValue(selected);
            if (normalized && !(catalogs[type] || []).includes(normalized)) ensureCatalogValue(type, normalized);
            return sortAlpha(catalogs[type] || []).map((item) => `<option value="${escapeAttr(item)}" ${item === normalized ? 'selected' : ''}>${item}</option>`).join('');
        };

        const getDisplayTasks = () => {
            const orderIndex = (type, value) => {
                const list = catalogs[type] || [];
                const idx = list.indexOf(value || '');
                return idx === -1 ? Number.MAX_SAFE_INTEGER : idx;
            };
            return tasks.map((task, index) => ({ task, index })).sort((aWrap, bWrap) => {
                const a = aWrap.task;
                const b = bWrap.task;
                const etapaOrder = orderIndex('etapas', a.etapa) - orderIndex('etapas', b.etapa);
                if (etapaOrder !== 0) return etapaOrder;
                const setorOrder = orderIndex('setores', a.setor) - orderIndex('setores', b.setor);
                if (setorOrder !== 0) return setorOrder;
                return aWrap.index - bWrap.index;
            }).map((entry) => entry.task);
        };

        const buildRows = () => {
            const displayTasks = getDisplayTasks();
            const rows = [];
            const etapas = [...new Set(displayTasks.map((task) => task.etapa || 'Sem etapa'))];
            etapas.forEach((etapa) => {
                rows.push({ type: 'etapa', etapa });
                if (collapsedEtapas.has(etapa)) return;
                const setorItems = displayTasks.filter((task) => (task.etapa || 'Sem etapa') === etapa);
                const setores = [...new Set(setorItems.map((task) => task.setor || 'Sem setor'))];
                setores.forEach((setor) => {
                    const setorKey = `${etapa}||${setor}`;
                    rows.push({ type: 'setor', etapa, setor, setorKey });
                    if (collapsedSetores.has(setorKey)) return;
                    setorItems.filter((task) => (task.setor || 'Sem setor') === setor).forEach((task) => rows.push({ type: 'task', task }));
                });
            });
            return rows;
        };

        const getScaleConfig = () => activeScale === 'semanas' ? { pxPerDay: 20 } : activeScale === 'meses' ? { pxPerDay: 4 } : { pxPerDay: 24 };
        const getTimelineRange = () => {
            const validDates = tasks.flatMap((task) => [parseDateInput(task.inicio), parseDateInput(task.fim)]).filter((d) => !Number.isNaN(d.getTime()));
            if (!validDates.length) { const min = addDays(today, -2); return { minDate: min, maxDate: addDays(min, MIN_DAYS - 1) }; }
            const sorted = [...validDates].sort((a, b) => a - b);
            const minDate = new Date(sorted[0]);
            const maxDate = new Date(sorted[sorted.length - 1]);
            const total = diffDays(minDate, maxDate) + 1;
            if (total < MIN_DAYS) return { minDate, maxDate: addDays(minDate, MIN_DAYS - 1) };
            return { minDate, maxDate };
        };
        const buildHeaderSegments = (minDate, maxDate, pxPerDay) => {
            const segments = [];
            const start = new Date(minDate.getFullYear(), minDate.getMonth(), minDate.getDate());
            const end = new Date(maxDate.getFullYear(), maxDate.getMonth(), maxDate.getDate());
            if (activeScale === 'meses') {
                let cursor = new Date(start);
                while (cursor <= end) {
                    const monthEnd = endOfMonth(cursor);
                    const segmentEnd = monthEnd < end ? monthEnd : end;
                    const days = diffDays(cursor, segmentEnd) + 1;
                    segments.push({ width: days * pxPerDay, label: new Intl.DateTimeFormat('pt-BR', { month: 'short', year: 'numeric' }).format(cursor) });
                    cursor = addDays(segmentEnd, 1);
                }
                return segments;
            }
            if (activeScale === 'semanas') {
                let cursor = new Date(start);
                while (cursor <= end) {
                    const segmentEnd = addDays(cursor, 6) < end ? addDays(cursor, 6) : end;
                    const days = diffDays(cursor, segmentEnd) + 1;
                    segments.push({ width: days * pxPerDay, label: `${pad(cursor.getDate())}/${pad(cursor.getMonth() + 1)}-${pad(segmentEnd.getDate())}/${pad(segmentEnd.getMonth() + 1)}` });
                    cursor = addDays(segmentEnd, 1);
                }
                return segments;
            }
            let cursor = new Date(start);
            while (cursor <= end) {
                const isWeekend = cursor.getDay() === 0 || cursor.getDay() === 6;
                const isFirstVisibleDay = cursor.getTime() === start.getTime();
                const isFirstDayOfMonth = cursor.getDate() === 1;
                const label = (isFirstVisibleDay || isFirstDayOfMonth)
                    ? `${pad(cursor.getDate())}/${pad(cursor.getMonth() + 1)}`
                    : `${pad(cursor.getDate())}`;
                segments.push({ width: pxPerDay, label, className: isWeekend ? 'is-weekend' : '' });
                cursor = addDays(cursor, 1);
            }
            return segments;
        };

        const fillSelectOptions = (select, options, selected = '') => {
            const current = selected || select.value || '';
            select.innerHTML = sortAlpha(options || []).map((item) => `<option value="${escapeAttr(item)}" ${item === current ? 'selected' : ''}>${item}</option>`).join('');
        };

        const renderCadastrosPanel = () => {
            cadastroActiveTitle.textContent = cadastroLabels[activeCadastroType] || 'Cadastro';
            cadastroActiveInput.placeholder = cadastroPlaceholders[activeCadastroType] || 'Novo item';
            const items = sortAlpha(catalogs[activeCadastroType] || []);
            cadastroActiveList.innerHTML = items.map((item) => `<li><span>${item}</span><div class="cadastro-item-actions"><button type="button" data-action="remove-catalog-item" data-catalog-type="${activeCadastroType}" data-catalog-value="${escapeAttr(item)}" title="Excluir">x</button></div></li>`).join('');
        };

        const renderTable = (rows) => {
            tableBody.innerHTML = rows.map((row) => {
                if (row.type === 'etapa') {
                    const isCollapsed = collapsedEtapas.has(row.etapa);
                    return `<tr class="group-row etapa-row"><td colspan="8"><div class="group-row-content"><div class="group-title-area"><button type="button" class="group-toggle" data-action="toggle-group" data-group-type="etapa" data-group-key="${encodeURIComponent(row.etapa)}"><span class="group-icon">${isCollapsed ? '+' : '-'}</span><strong>${row.etapa}</strong></button><div class="group-order-actions"><button type="button" data-action="add-row-group" data-etapa="${escapeAttr(row.etapa)}" title="Nova linha nesta etapa">+</button><button type="button" data-action="move-group" data-catalog-type="etapas" data-group-value="${escapeAttr(row.etapa)}" data-direction="up" title="Subir etapa">^</button><button type="button" data-action="move-group" data-catalog-type="etapas" data-group-value="${escapeAttr(row.etapa)}" data-direction="down" title="Descer etapa">v</button></div></div></div></td></tr>`;
                }
                if (row.type === 'setor') {
                    const isCollapsed = collapsedSetores.has(row.setorKey);
                    return `<tr class="group-row setor-row"><td colspan="8"><div class="group-row-content"><div class="group-title-area"><button type="button" class="group-toggle" data-action="toggle-group" data-group-type="setor" data-group-key="${encodeURIComponent(row.setorKey)}"><span class="group-icon">${isCollapsed ? '+' : '-'}</span><strong>${row.setor}</strong></button><div class="group-order-actions"><button type="button" data-action="add-row-group" data-etapa="${escapeAttr(row.etapa)}" data-setor="${escapeAttr(row.setor)}" title="Nova linha neste setor">+</button><button type="button" data-action="move-group" data-catalog-type="setores" data-group-value="${escapeAttr(row.setor)}" data-direction="up" title="Subir setor">^</button><button type="button" data-action="move-group" data-catalog-type="setores" data-group-value="${escapeAttr(row.setor)}" data-direction="down" title="Descer setor">v</button></div></div></div></td></tr>`;
                }
                const task = row.task;
                normalizeTaskDates(task);
                const hasDependency = task.dependenciaTaskId !== null && task.dependenciaTaskId !== undefined;
                const dependencyTooltip = escapeAttr(getDependencyTooltip(task));
                return `<tr data-id="${task.id}" class="${hasDependency ? 'has-dependency' : ''}" title="${dependencyTooltip}" data-dep-task-id="${hasDependency ? Number(task.dependenciaTaskId) : ''}" data-dep-tipo="${hasDependency ? task.dependenciaTipo : ''}"><td><div class="row-actions"><button type="button" class="edit-row-btn" data-action="edit-row" title="Editar etapa e setor">&#9998;</button><button type="button" class="drag-row-btn" draggable="true" data-action="start-row-drag" title="Arraste para ordenar">::</button></div></td><td><select data-field="tarefa">${toSelectOptions('tarefas', task.tarefa)}</select></td><td><select data-field="subtarefa">${toSelectOptions('subtarefas', task.subtarefa)}</select></td><td><input type="number" data-field="duracao" min="0" step="1" value="${Math.max(0, Number(task.duracao) || 0)}"></td><td><div class="date-cell" data-link-target="inicio" data-task-id="${task.id}"><button type="button" class="link-handle" draggable="true" data-action="start-link-drag" data-task-id="${task.id}" title="Arraste para Inicio ou Termino de outra tarefa">o</button><input type="date" data-field="inicio" value="${task.inicio || ''}" ${hasDependency ? 'disabled' : ''}><span class="date-weekday">${getWeekDay(task.inicio)}</span></div></td><td><div class="date-cell readonly" data-link-target="termino" data-task-id="${task.id}"><span class="readonly-pill">${toDateBr(task.fim)}</span><span class="date-weekday">${getWeekDay(task.fim)}</span></div></td><td><div class="interval-wrap"><input type="number" data-field="intervalo" step="1" value="${Math.round(Number(task.intervalo) || 0)}">${hasDependency ? '<button type="button" class="unlink-btn" data-action="unlink" title="Remover vinculo">Desv.</button>' : ''}</div></td><td><button type="button" class="delete-btn" data-action="delete">x</button></td></tr>`;
            }).join('');
        };

        const renderTimeline = (rows) => {
            const { minDate, maxDate } = getTimelineRange();
            const { pxPerDay } = getScaleConfig();
            const totalDays = diffDays(minDate, maxDate) + 1;
            const totalWidth = totalDays * pxPerDay;
            timelineHeader.style.width = `${totalWidth}px`;
            timelineRows.style.width = `${totalWidth}px`;
            timelineBodyScroll.style.backgroundSize = `${pxPerDay}px 100%`;
            timelineHeader.dataset.scale = activeScale;
            const segments = buildHeaderSegments(minDate, maxDate, pxPerDay);
            timelineHeader.innerHTML = `<div class="day-grid">${segments.map((s) => `<div class="day-cell ${s.className || ''}" style="width:${s.width}px;">${s.label}</div>`).join('')}</div>`;
            timelineRows.innerHTML = rows.map((row) => {
                if (row.type === 'etapa') return '<div class="timeline-row timeline-group-row timeline-etapa-row"></div>';
                if (row.type === 'setor') return '<div class="timeline-row timeline-group-row timeline-setor-row"></div>';
                const task = row.task;
                normalizeTaskDates(task);
                const start = parseDateInput(task.inicio);
                const end = parseDateInput(task.fim);
                const startOffset = Math.max(0, diffDays(minDate, start));
                const spanDays = Math.max(1, diffDays(start, end) + 1);
                return `<div class="timeline-row"><div class="timeline-bar" style="left:${startOffset * pxPerDay}px;width:${spanDays * pxPerDay}px;"><div class="timeline-progress" style="width:100%;"></div></div></div>`;
            }).join('');
        };

        const syncTimelineRowHeights = () => {
            const tableRows = Array.from(tableBody.querySelectorAll('tr'));
            const graphRows = Array.from(timelineRows.querySelectorAll('.timeline-row'));
            const count = Math.min(tableRows.length, graphRows.length);

            for (let i = 0; i < count; i += 1) {
                const tableHeight = Math.round(tableRows[i].getBoundingClientRect().height);
                if (tableHeight > 0) {
                    graphRows[i].style.height = `${tableHeight - 1}px`;
                }
            }

            graphRows.forEach((row) => {
                const bar = row.querySelector('.timeline-bar');
                if (!bar) return;
                const rowHeight = row.getBoundingClientRect().height;
                const barHeight = Math.max(10, Math.min(18, Math.round(rowHeight - 8)));
                bar.style.height = `${barHeight}px`;
                bar.style.top = `${Math.max(2, Math.round((rowHeight - barHeight) / 2))}px`;
            });
        };

        const renderAll = () => {
            syncDependentDates();
            saveTasks();
            const rows = buildRows();
            renderTable(rows);
            renderTimeline(rows);
            requestAnimationFrame(syncTimelineRowHeights);
        };

        const openRowEditModal = (task) => { editingRowId = Number(task.id); fillSelectOptions(rowEditEtapaSelect, catalogs.etapas, task.etapa || catalogs.etapas?.[0] || ''); fillSelectOptions(rowEditSetorSelect, catalogs.setores, task.setor || catalogs.setores?.[0] || ''); rowEditModal.classList.remove('is-hidden'); rowEditModal.setAttribute('aria-hidden', 'false'); };
        const closeRowEditModal = () => { editingRowId = null; rowEditModal.classList.add('is-hidden'); rowEditModal.setAttribute('aria-hidden', 'true'); };
        const openNewRowModal = () => { fillSelectOptions(newRowEtapaSelect, catalogs.etapas, catalogs.etapas?.[0] || 'Unica'); fillSelectOptions(newRowSetorSelect, catalogs.setores, catalogs.setores?.[0] || ''); newRowModal.classList.remove('is-hidden'); newRowModal.setAttribute('aria-hidden', 'false'); };
        const closeNewRowModal = () => { newRowModal.classList.add('is-hidden'); newRowModal.setAttribute('aria-hidden', 'true'); };
        const closeCadastroMenu = () => cadastroMenu.classList.add('is-hidden');
        const closeCadastroModal = () => { cadastrosModal.classList.add('is-hidden'); cadastrosModal.setAttribute('aria-hidden', 'true'); };
        const openCadastroModal = (type) => { activeCadastroType = type; renderCadastrosPanel(); cadastrosModal.classList.remove('is-hidden'); cadastrosModal.setAttribute('aria-hidden', 'false'); };

        const clearSourceHoverHighlight = () => { if (!hoveredSourceCell) return; hoveredSourceCell.classList.remove('source-link-cell-hover'); hoveredSourceCell = null; };
        const applySourceHoverHighlightFromRow = (row) => {
            clearSourceHoverHighlight();
            if (!row || !row.classList.contains('has-dependency')) return;
            const depTaskId = Number(row.dataset.depTaskId || 0);
            const depTipo = row.dataset.depTipo || '';
            if (!depTaskId || (depTipo !== 'inicio' && depTipo !== 'termino')) return;
            const sourceDateCell = tableBody.querySelector(`.date-cell[data-task-id="${depTaskId}"][data-link-target="${depTipo}"]`);
            const sourceCell = sourceDateCell?.closest('td');
            if (!sourceCell) return;
            sourceCell.classList.add('source-link-cell-hover');
            hoveredSourceCell = sourceCell;
        };

        const updateTaskFromField = (event) => {
            const fieldElement = event.target.closest('[data-field]');
            if (!fieldElement) return;
            const isInputEvent = event.type === 'input';
            const row = fieldElement.closest('tr[data-id]');
            if (!row) return;
            const id = Number(row.dataset.id);
            const task = tasks.find((item) => Number(item.id) === id);
            if (!task) return;
            const field = fieldElement.dataset.field;
            if (field === 'duracao') {
                if (isInputEvent) return;
                task[field] = Math.max(0, Math.round(Number(fieldElement.value) || 0));
                fieldElement.value = task[field];
                normalizeTaskDates(task);
            } else if (field === 'intervalo') {
                if (isInputEvent) return;
                task[field] = Math.round(Number(fieldElement.value) || 0);
                fieldElement.value = task[field];
            } else {
                task[field] = fieldElement.value;
                if (field === 'tarefa') ensureCatalogValue('tarefas', task[field]);
                if (field === 'subtarefa') ensureCatalogValue('subtarefas', task[field]);
            }
            if (field === 'inicio') { normalizeTaskDates(task); renderAll(); return; }
            if (field === 'duracao' || field === 'intervalo') { renderAll(); return; }
        };

        tableBody.addEventListener('input', updateTaskFromField);
        tableBody.addEventListener('change', updateTaskFromField);
        addTaskBtn.addEventListener('click', openNewRowModal);
        newRowModal.addEventListener('click', (event) => { if (event.target.closest('[data-action="close-new-row"]')) closeNewRowModal(); });
        newRowForm.addEventListener('submit', (event) => { event.preventDefault(); tasks.push(createTask(newRowEtapaSelect.value, newRowSetorSelect.value)); saveCatalogs(); closeNewRowModal(); renderAll(); });

        tableBody.addEventListener('click', (event) => {
            const button = event.target.closest('button[data-action="delete"]');
            const groupToggle = event.target.closest('button[data-action="toggle-group"]');
            const unlinkButton = event.target.closest('button[data-action="unlink"]');
            const groupMoveButton = event.target.closest('button[data-action="move-group"]');
            const addRowGroupButton = event.target.closest('button[data-action="add-row-group"]');
            const editRowButton = event.target.closest('button[data-action="edit-row"]');
            if (addRowGroupButton) { const etapa = normalizeCatalogValue(addRowGroupButton.dataset.etapa) || (catalogs.etapas?.[0] || 'Unica'); const setor = normalizeCatalogValue(addRowGroupButton.dataset.setor) || (catalogs.setores?.[0] || ''); tasks.push(createTask(etapa, setor)); saveCatalogs(); renderAll(); return; }
            if (groupMoveButton) { if (moveCatalogItem(groupMoveButton.dataset.catalogType, normalizeCatalogValue(groupMoveButton.dataset.groupValue), groupMoveButton.dataset.direction)) renderAll(); return; }
            if (groupToggle) { const type = groupToggle.dataset.groupType; const key = decodeURIComponent(groupToggle.dataset.groupKey || ''); if (type === 'etapa') { if (collapsedEtapas.has(key)) collapsedEtapas.delete(key); else collapsedEtapas.add(key); renderAll(); return; } if (type === 'setor') { if (collapsedSetores.has(key)) collapsedSetores.delete(key); else collapsedSetores.add(key); renderAll(); return; } }
            if (unlinkButton) { const row = unlinkButton.closest('tr[data-id]'); const task = row ? tasks.find((item) => Number(item.id) === Number(row.dataset.id)) : null; if (!task) return; task.dependenciaTaskId = null; task.dependenciaTipo = null; task.intervalo = 0; renderAll(); return; }
            if (editRowButton) { const row = editRowButton.closest('tr[data-id]'); const task = row ? tasks.find((item) => Number(item.id) === Number(row.dataset.id)) : null; if (task) openRowEditModal(task); return; }
            if (!button) return;
            const row = button.closest('tr[data-id]');
            if (!row) return;
            const id = Number(row.dataset.id);
            tasks = tasks.filter((task) => Number(task.id) !== id);
            tasks.forEach((task) => { if (Number(task.dependenciaTaskId) === id) { task.dependenciaTaskId = null; task.dependenciaTipo = null; task.intervalo = 0; } });
            if (!tasks.length) tasks.push(createTask(catalogs.etapas?.[0], catalogs.setores?.[0]));
            renderAll();
        });

        tableBody.addEventListener('dragstart', (event) => {
            const target = event.target instanceof Element ? event.target : null;
            if (!target) return;
            const linkHandle = target.closest('button[data-action="start-link-drag"]');
            if (linkHandle) { linkSourceTaskId = Number(linkHandle.dataset.taskId); reorderTaskId = null; if (event.dataTransfer) { event.dataTransfer.effectAllowed = 'link'; event.dataTransfer.setData('text/plain', String(linkSourceTaskId)); } return; }
            const rowDragHandle = target.closest('button[data-action="start-row-drag"]');
            const row = target.closest('tr[data-id]');
            if (!rowDragHandle || !row) { reorderTaskId = null; return; }
            reorderTaskId = Number(row.dataset.id); linkSourceTaskId = null;
            if (event.dataTransfer) { event.dataTransfer.effectAllowed = 'move'; event.dataTransfer.setData('text/plain', String(reorderTaskId)); }
        });
        tableBody.addEventListener('dragend', () => { linkSourceTaskId = null; reorderTaskId = null; tableBody.querySelectorAll('.is-drop-target').forEach((el) => el.classList.remove('is-drop-target')); tableBody.querySelectorAll('.row-drop-target').forEach((el) => el.classList.remove('row-drop-target')); });
        tableBody.addEventListener('mouseover', (event) => applySourceHoverHighlightFromRow(event.target.closest('tr.has-dependency')));
        tableBody.addEventListener('mouseout', (event) => { const row = event.target.closest('tr.has-dependency'); if (!row) return; const related = event.relatedTarget; if (related && row.contains(related)) return; clearSourceHoverHighlight(); });
        tableBody.addEventListener('dragover', (event) => {
            if (reorderTaskId) {
                const targetRow = event.target.closest('tr[data-id]');
                if (!targetRow) return;
                const targetId = Number(targetRow.dataset.id);
                if (!targetId || targetId === reorderTaskId) return;
                event.preventDefault();
                tableBody.querySelectorAll('.row-drop-target').forEach((el) => el.classList.remove('row-drop-target'));
                targetRow.classList.add('row-drop-target');
                return;
            }
            const target = event.target.closest('[data-link-target][data-task-id]');
            if (!target || !linkSourceTaskId) return;
            event.preventDefault();
            target.classList.add('is-drop-target');
        });
        tableBody.addEventListener('dragleave', (event) => { const targetRow = event.target.closest('tr[data-id]'); if (targetRow) targetRow.classList.remove('row-drop-target'); const target = event.target.closest('[data-link-target][data-task-id]'); if (target) target.classList.remove('is-drop-target'); });
        tableBody.addEventListener('drop', (event) => {
            if (reorderTaskId) {
                const targetRow = event.target.closest('tr[data-id]');
                if (!targetRow) { reorderTaskId = null; return; }
                event.preventDefault();
                const targetId = Number(targetRow.dataset.id);
                targetRow.classList.remove('row-drop-target');
                const moved = moveTaskBefore(reorderTaskId, targetId);
                reorderTaskId = null;
                if (moved) renderAll();
                return;
            }
            const target = event.target.closest('[data-link-target][data-task-id]');
            if (!target || !linkSourceTaskId) return;
            event.preventDefault();
            target.classList.remove('is-drop-target');
            const predecessorId = Number(target.dataset.taskId);
            const dependencyType = target.dataset.linkTarget;
            const dependentTask = tasks.find((task) => Number(task.id) === Number(linkSourceTaskId));
            linkSourceTaskId = null;
            if (!dependentTask) return;
            if (!predecessorId || predecessorId === Number(dependentTask.id)) return;
            if (dependencyType !== 'inicio' && dependencyType !== 'termino') return;
            dependentTask.dependenciaTaskId = predecessorId;
            dependentTask.dependenciaTipo = dependencyType;
            dependentTask.intervalo = Math.round(Number(dependentTask.intervalo) || 0);
            renderAll();
        });

        let syncingVertical = false;
        let syncingHorizontal = false;
        sheetScroll.addEventListener('scroll', () => { if (syncingVertical) return; syncingVertical = true; timelineBodyScroll.scrollTop = sheetScroll.scrollTop; syncingVertical = false; });
        timelineBodyScroll.addEventListener('scroll', () => { if (!syncingVertical) { syncingVertical = true; sheetScroll.scrollTop = timelineBodyScroll.scrollTop; syncingVertical = false; } if (syncingHorizontal) return; syncingHorizontal = true; timelineHeaderScroll.scrollLeft = timelineBodyScroll.scrollLeft; syncingHorizontal = false; });
        timelineHeaderScroll.addEventListener('scroll', () => { if (syncingHorizontal) return; syncingHorizontal = true; timelineBodyScroll.scrollLeft = timelineHeaderScroll.scrollLeft; syncingHorizontal = false; });
        window.addEventListener('resize', () => { requestAnimationFrame(syncTimelineRowHeights); });

        openCadastrosBtn.addEventListener('click', () => cadastroMenu.classList.toggle('is-hidden'));
        cadastroMenu.addEventListener('click', (event) => { const item = event.target.closest('button[data-open-cadastro-type]'); if (!item) return; closeCadastroMenu(); openCadastroModal(item.dataset.openCadastroType); });
        document.addEventListener('click', (event) => { if (!cadastroMenu.contains(event.target) && event.target !== openCadastrosBtn) closeCadastroMenu(); });
        scaleButtons.forEach((button) => button.addEventListener('click', () => { const scale = button.dataset.scale; if (!scale) return; activeScale = scale; scaleButtons.forEach((item) => item.classList.toggle('is-active', item === button)); renderTimeline(buildRows()); }));

        cadastrosModal.addEventListener('click', (event) => {
            if (event.target.closest('[data-action="close-cadastros"]')) { closeCadastroModal(); return; }
            const removeBtn = event.target.closest('button[data-action="remove-catalog-item"]');
            if (!removeBtn) return;
            const type = removeBtn.dataset.catalogType;
            const value = normalizeCatalogValue(removeBtn.dataset.catalogValue);
            if (!type || !value || !catalogs[type]) return;
            if ((catalogs[type] || []).length <= 1) return;
            catalogs[type] = catalogs[type].filter((item) => item !== value);
            tasks.forEach((task) => { if (type === 'etapas' && task.etapa === value) task.etapa = catalogs.etapas[0] || ''; if (type === 'setores' && task.setor === value) task.setor = catalogs.setores[0] || ''; if (type === 'tarefas' && task.tarefa === value) task.tarefa = catalogs.tarefas[0] || ''; if (type === 'subtarefas' && task.subtarefa === value) task.subtarefa = catalogs.subtarefas[0] || ''; });
            saveCatalogs();
            renderCadastrosPanel();
            renderAll();
        });

        rowEditModal.addEventListener('click', (event) => { if (event.target.closest('[data-action="close-row-edit"]')) closeRowEditModal(); });
        rowEditForm.addEventListener('submit', (event) => { event.preventDefault(); const etapa = normalizeCatalogValue(rowEditEtapaSelect.value); const setor = normalizeCatalogValue(rowEditSetorSelect.value); if (!etapa || !setor || editingRowId === null) return; const task = tasks.find((item) => Number(item.id) === Number(editingRowId)); if (!task) return; task.etapa = etapa; task.setor = setor; ensureCatalogValue('etapas', etapa); ensureCatalogValue('setores', setor); saveCatalogs(); closeRowEditModal(); renderAll(); });
        cadastroActiveForm.addEventListener('submit', (event) => { event.preventDefault(); const value = normalizeCatalogValue(cadastroActiveInput.value); if (!activeCadastroType || !value) return; ensureCatalogValue(activeCadastroType, value); saveCatalogs(); renderCadastrosPanel(); renderAll(); cadastroActiveInput.value = ''; });

        loadCatalogs();
        loadTasks();
        renderAll();
    </script>
</body>
</html>
