<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeto de cobertura</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/welcome.css">
</head>
<body>
    <main class="home-page">
        <section class="home-shell" aria-label="Pagina inicial">
            <header class="home-topbar">
                <p class="brand-tag">Projeto de cobertura</p>
                <h1>Bem-vindo!</h1>
                <p class="subtitle">Sistema com area de login e dashboard no mesmo visual.</p>
            </header>

            <section class="home-actions">
                <a href="/login" class="btn btn-primary">Entrar</a>
                @auth
                    <a href="/dashboard" class="btn btn-outline">Ir para dashboard</a>
                @endauth
            </section>
        </section>
    </main>
</body>
</html>
