<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Projeto de cobertura</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>
    <main class="dashboard-page">
        <section class="dashboard-shell" aria-label="Dashboard">
            <header class="dashboard-topbar">
                <div>
                    <p class="brand-tag">Projeto de cobertura</p>
                    <h1>Bem-vindo!</h1>
                </div>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="btn-outline">Sair</button>
                </form>
            </header>

            <section class="dashboard-card">
                <h2>Selecione o tipo de cobertura:</h2>

                <div class="coverage-options">
                    <button type="button" class="coverage-btn">Metálica</button>
                    <button type="button" class="coverage-btn">Concreto</button>
                </div>
            </section>
        </section>
    </main>
</body>
</html>

