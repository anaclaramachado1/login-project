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

  <main v-else class="dashboard-page">
    <header class="topbar">
      <div class="topbar-left">
        <strong>Painel</strong>
        <span class="topbar-user">{{ user.name }}</span>
      </div>

      <div class="topbar-actions">
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
</template>

<script setup>
import { onMounted, ref } from 'vue'

const email = ref('')
const password = ref('')
const remember = ref(false)
const user = ref(null)
const loading = ref(false)
const error = ref('')
const message = ref('')
const showCadastroMenu = ref(false)
const activeCadastro = ref('')

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

async function loadUser() {
  try {
    const data = await api('/api/me')
    user.value = data.user
  } catch {
    user.value = null
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

    await loadUser()
    message.value = 'Login realizado com sucesso.'
    password.value = ''
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function logout() {
  loading.value = true
  error.value = ''
  message.value = ''

  try {
    await api('/api/logout', { method: 'POST' })
    user.value = null
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

onMounted(loadUser)
</script>
