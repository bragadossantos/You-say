<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessão Expirada — YouSay</title>
    {{-- Redireciona automaticamente para a página anterior (pedido novo ao servidor, nunca a partir da cache) --}}
    <meta http-equiv="refresh" content="2;url={{ url()->previous() }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 48px 40px;
            text-align: center;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
        }
        .icon { font-size: 3rem; margin-bottom: 16px; }
        h1 { font-size: 1.4rem; font-weight: 600; color: #f8fafc; margin-bottom: 12px; }
        p { color: #94a3b8; font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px; }
        .spinner {
            width: 36px; height: 36px;
            border: 3px solid #334155;
            border-top-color: #f97316;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 16px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        a {
            display: inline-block;
            background: #f97316;
            color: white;
            padding: 10px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        a:hover { background: #ea6a0a; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⏰</div>
        <h1>Sessão Expirada</h1>
        <p>A sua sessão expirou por inatividade. A redirecionar automaticamente...</p>
        <div class="spinner"></div>
        <a href="{{ url()->previous() }}">Voltar e tentar novamente</a>
    </div>
    <script>
        // Volta à página anterior com um pedido novo ao servidor (token CSRF fresco)
        setTimeout(function () {
            window.location.href = @json(url()->previous());
        }, 1500);
    </script>
</body>
</html>
