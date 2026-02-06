<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Projeto de cobertura</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
    <main class="auth-page">
        <section class="login-shell" aria-label="Tela de login">
            <aside class="login-brand">
                <p class="brand-tag">Projeto de cobertura</p>
                <h1>Bem-vindo!</h1>
                <p class="brand-text">Entre com seu email e senha para continuar acessando seu painel.</p>
            </aside>

            <section class="login-card">
                <h2>Entrar</h2>
                <p class="form-subtitle">Use os dados da sua conta para acessar.</p>

                @if (session('error'))
                    <div class="alert-error" role="alert">{{ session('error') }}</div>
                @endif

                <form method="POST" action="/login" novalidate>
                    @csrf

                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="voce@email.com"
                        autocomplete="email"
                        required
                    >

                    <label for="password">Senha</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Sua senha"
                        autocomplete="current-password"
                        required
                    >

                    <button type="submit">Entrar</button>
                </form>
            </section>
        </section>
    </main>
</body>
</html>

