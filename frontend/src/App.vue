<template>
  <main v-if="!user" class="login-container">
    <h2>Login</h2>

    <p v-if="message" class="status success">{{ message }}</p>
    <p v-if="error" class="status error">{{ error }}</p>

    <form @submit.prevent="login">
      <input
        v-model="email"
        type="email"
        placeholder="Email"
        autocomplete="email"
        required
      />

      <input
        v-model="password"
        type="password"
        placeholder="Senha"
        autocomplete="current-password"
        required
      />

      <label class="remember-row">
        <input v-model="remember" type="checkbox" />
        Lembrar de mim
      </label>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Entrando...' : 'Entrar' }}
      </button>
    </form>
  </main>

  <main v-else-if="!ambiente" class="ambiente-container">
    <h2>Escolha o ambiente</h2>
    <p class="ambiente-subtitle">Selecione para onde voce quer entrar.</p>

    <p v-if="message" class="status success">{{ message }}</p>
    <p v-if="error" class="status error">{{ error }}</p>

    <div class="ambiente-options">
      <button type="button" :disabled="loading" @click="selectAmbiente('vendas')">
        <strong>Gestao de vendas</strong>
        <span>Clientes, fornecedores, itens e cotacoes.</span>
      </button>

      <button type="button" :disabled="loading" @click="selectAmbiente('cronogramas')">
        <strong>Cronogramas</strong>
        <span>Planejamento, etapas e prazos.</span>
      </button>
    </div>
  </main>

  <main v-else-if="ambiente === 'vendas'" class="dashboard-page">
    <header class="topbar">
      <div class="topbar-left">
        <strong>Painel de Vendas</strong>
        <span class="topbar-user">{{ user.name }}</span>
      </div>

      <div class="topbar-actions">
        <button type="button" class="menu-button switch" @click="clearAmbiente" :disabled="loading">
          Trocar ambiente
        </button>
        <button type="button" @click="toggleCadastroMenu" class="menu-button">
          Cadastro
        </button>
        <button type="button" @click="logout" :disabled="loading" class="menu-button danger">
          {{ loading ? 'Saindo...' : 'Sair' }}
        </button>
      </div>
    </header>

    <section v-if="showCadastroMenu" class="submenu">
      <button
        type="button"
        class="submenu-button"
        :class="{ active: activeCadastro === 'clientes' }"
        @click="selectCadastro('clientes')"
      >
        Clientes
      </button>
      <button
        type="button"
        class="submenu-button"
        :class="{ active: activeCadastro === 'fornecedores' }"
        @click="selectCadastro('fornecedores')"
      >
        Fornecedores
      </button>
      <button
        type="button"
        class="submenu-button"
        :class="{ active: activeCadastro === 'itens' }"
        @click="selectCadastro('itens')"
      >
        Itens
      </button>
    </section>

    <section class="dashboard-content">
      <p v-if="message" class="status success">{{ message }}</p>
      <p v-if="error" class="status error">{{ error }}</p>

      <article v-if="activeCadastro === 'clientes'" class="content-card">
        <h3>Cadastro de Clientes</h3>
        <p>Proxima etapa: incluir formulario com nome, documento, telefone e email.</p>
      </article>

      <article v-else-if="activeCadastro === 'fornecedores'" class="content-card">
        <h3>Cadastro de Fornecedores</h3>
        <p>Proxima etapa: incluir formulario de dados comerciais e contato principal.</p>
      </article>

      <article v-else-if="activeCadastro === 'itens'" class="content-card">
        <h3>Cadastro de Itens</h3>
        <p>Proxima etapa: incluir formulario com descricao, codigo, preco e estoque.</p>
      </article>

      <article v-else class="content-card">
        <h3>Bem-vinda</h3>
        <p>Use o menu Cadastro para abrir as telas de Clientes, Fornecedores e Itens.</p>
      </article>
    </section>
  </main>

  <main v-else class="dashboard-page">
    <header class="topbar">
      <div class="topbar-left">
        <strong>Ambiente de Cronogramas</strong>
        <span class="topbar-user">{{ user.name }}</span>
      </div>

      <div class="topbar-actions">
        <button type="button" class="menu-button switch" @click="clearAmbiente" :disabled="loading">
          Trocar ambiente
        </button>
        <button type="button" @click="logout" :disabled="loading" class="menu-button danger">
          {{ loading ? 'Saindo...' : 'Sair' }}
        </button>
      </div>
    </header>

    <section class="dashboard-content cronograma-content">
      <p v-if="message" class="status success">{{ message }}</p>
      <p v-if="error" class="status error">{{ error }}</p>

      <section class="cronograma-layout">
        <article class="content-card cronograma-table-card">
          <div class="cronograma-card-header">
            <h3>Cronograma</h3>
            <button type="button" class="menu-button" @click="addCronogramaRow">Nova linha</button>
          </div>

          <div class="cronograma-table-wrap">
            <table class="cronograma-table">
              <thead>
                <tr>
                  <th>Obra</th>
                  <th>Etapa</th>
                  <th>Setor</th>
                  <th>Tarefa</th>
                  <th>Subtarefa</th>
                  <th>Inicio</th>
                  <th>Termino</th>
                  <th>Duracao</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in cronogramaRowsWithMetrics" :key="row.id">
                  <td><input v-model="row.obra" type="text" placeholder="Obra" /></td>
                  <td><input v-model="row.etapa" type="text" placeholder="Etapa" /></td>
                  <td><input v-model="row.setor" type="text" placeholder="Setor" /></td>
                  <td><input v-model="row.tarefa" type="text" placeholder="Tarefa" /></td>
                  <td><input v-model="row.subtarefa" type="text" placeholder="Subtarefa" /></td>
                  <td><input v-model="row.inicio" type="date" /></td>
                  <td><input v-model="row.termino" type="date" /></td>
                  <td class="duracao-cell">{{ row.durationLabel }}</td>
                  <td>
                    <button
                      type="button"
                      class="table-action"
                      @click="removeCronogramaRow(row.id)"
                      :disabled="cronogramaRows.length === 1"
                    >
                      X
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </article>

        <article class="content-card cronograma-chart-card">
          <h3>Grafico de barras</h3>

          <div class="timeline-list">
            <div v-for="row in cronogramaRowsWithMetrics" :key="`bar-${row.id}`" class="timeline-row">
              <div class="timeline-label">
                {{ row.tarefa || row.subtarefa || 'Sem tarefa' }}
              </div>
              <div class="timeline-track">
                <div
                  v-if="row.hasValidRange"
                  class="timeline-bar"
                  :style="{
                    marginLeft: `${row.offsetDays * 22}px`,
                    width: `${Math.max((row.durationDays + 1) * 22, 10)}px`,
                  }"
                />
              </div>
            </div>
          </div>
        </article>
      </section>
    </section>
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'

const email = ref('')
const password = ref('')
const remember = ref(false)
const user = ref(null)
const ambiente = ref('')
const loading = ref(false)
const error = ref('')
const message = ref('')
const showCadastroMenu = ref(false)
const activeCadastro = ref('')
const cronogramaRows = ref([
  {
    id: 1,
    obra: '',
    etapa: '',
    setor: '',
    tarefa: '',
    subtarefa: '',
    inicio: '',
    termino: '',
  },
])

const timelineStart = computed(() => {
  let minDate = null

  for (const row of cronogramaRows.value) {
    const start = parseDateInput(row.inicio)
    if (!start) {
      continue
    }

    if (!minDate || start < minDate) {
      minDate = start
    }
  }

  return minDate
})

const cronogramaRowsWithMetrics = computed(() => {
  return cronogramaRows.value.map((row) => {
    const start = parseDateInput(row.inicio)
    const end = parseDateInput(row.termino)
    const hasValidRange = Boolean(start && end && end >= start && timelineStart.value)
    const durationDays = hasValidRange ? diffInDays(start, end) : null
    const offsetDays = hasValidRange ? diffInDays(timelineStart.value, start) : 0

    return {
      ...row,
      hasValidRange,
      durationDays,
      offsetDays,
      durationLabel: durationDays === null ? '-' : String(durationDays),
    }
  })
})

async function api(path, options = {}) {
  const response = await fetch(path, {
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json',
      ...(options.headers || {}),
    },
    ...options,
  })

  const data = await response.json().catch(() => ({}))

  if (!response.ok) {
    throw new Error(data.message || 'Falha na requisicao.')
  }

  return data
}

async function loadSession() {
  try {
    const data = await api('/api/me')
    user.value = data.user
    ambiente.value = data.ambiente || ''
  } catch {
    user.value = null
    ambiente.value = ''
  }
}

async function login() {
  loading.value = true
  error.value = ''
  message.value = ''

  try {
    await api('/api/login', {
      method: 'POST',
      body: JSON.stringify({
        email: email.value,
        password: password.value,
        remember: remember.value,
      }),
    })

    await loadSession()
    message.value = 'Login realizado com sucesso.'
    password.value = ''
    showCadastroMenu.value = false
    activeCadastro.value = ''
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function selectAmbiente(nextAmbiente) {
  loading.value = true
  error.value = ''
  message.value = ''

  try {
    const data = await api('/api/ambiente', {
      method: 'POST',
      body: JSON.stringify({ ambiente: nextAmbiente }),
    })

    ambiente.value = data.ambiente
    message.value = data.message || 'Ambiente selecionado com sucesso.'
    showCadastroMenu.value = false
    activeCadastro.value = ''
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function clearAmbiente() {
  ambiente.value = ''
  showCadastroMenu.value = false
  activeCadastro.value = ''
  message.value = ''
  error.value = ''
}

async function logout() {
  loading.value = true
  error.value = ''
  message.value = ''

  try {
    await api('/api/logout', { method: 'POST' })
    user.value = null
    ambiente.value = ''
    showCadastroMenu.value = false
    activeCadastro.value = ''
    message.value = 'Voce saiu da sessao.'
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

function toggleCadastroMenu() {
  showCadastroMenu.value = !showCadastroMenu.value
}

function selectCadastro(section) {
  activeCadastro.value = section
}

function parseDateInput(value) {
  if (!value) {
    return null
  }

  const parsed = new Date(`${value}T00:00:00`)
  return Number.isNaN(parsed.getTime()) ? null : parsed
}

function diffInDays(start, end) {
  const MS_PER_DAY = 1000 * 60 * 60 * 24
  return Math.round((end.getTime() - start.getTime()) / MS_PER_DAY)
}

function addCronogramaRow() {
  cronogramaRows.value.push({
    id: Date.now() + Math.floor(Math.random() * 1000),
    obra: '',
    etapa: '',
    setor: '',
    tarefa: '',
    subtarefa: '',
    inicio: '',
    termino: '',
  })
}

function removeCronogramaRow(rowId) {
  if (cronogramaRows.value.length === 1) {
    return
  }

  cronogramaRows.value = cronogramaRows.value.filter((row) => row.id !== rowId)
}

onMounted(loadSession)
</script>
