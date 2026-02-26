<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecionar ambiente</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .card {
            width: 100%;
            max-width: 520px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 24px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 24px;
        }

        p {
            margin-top: 0;
            color: #475569;
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 12px;
        }

        .options {
            display: grid;
            gap: 12px;
            margin-top: 16px;
        }

        .option-form {
            margin: 0;
        }

        button {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            padding: 14px;
            text-align: left;
            cursor: pointer;
        }

        button strong {
            display: block;
            margin-bottom: 4px;
            font-size: 16px;
        }

        button small {
            color: #64748b;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Escolha o ambiente</h1>
        <p>Usuaria: <strong>{{ $user->name }}</strong></p>

        @if (session('error'))
            <div class="error" role="alert">{{ session('error') }}</div>
        @endif

        <div class="options">
            <form class="option-form" method="POST" action="/selecionar-ambiente">
                @csrf
                <input type="hidden" name="ambiente" value="vendas">
                <button type="submit">
                    <strong>Gestao de vendas</strong>
                    <small>Acessa clientes, fornecedores, itens e cotacoes.</small>
                </button>
            </form>

            <form class="option-form" method="POST" action="/selecionar-ambiente">
                @csrf
                <input type="hidden" name="ambiente" value="cronogramas">
                <button type="submit">
                    <strong>Cronogramas</strong>
                    <small>Acessa o ambiente de planejamento e cronogramas.</small>
                </button>
            </form>
        </div>
    </main>
</body>
</html>
