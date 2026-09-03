<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de PHP - UFCD 9952</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0e1117;
            color: #c9d1d9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background-color: #161b22;
            border: 1px solid #30363d;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            max-width: 400px;
        }
        h1 {
            color: #58a6ff;
            margin-top: 0;
        }
        .status {
            background-color: #238636;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }
        .time-box {
            background-color: #21262d;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            border: 1px solid #30363d;
        }
    </style>
</head>
<body>

    <div class="card">
        <span class="status">Servidor PHP Ativo! ✅</span>
        <h1>Olá, Mundo!</h1>
        <p>O seu primeiro Codespace para a <strong>UFCD 9952</strong> está configurado e a funcionar.</p>
        
        <div class="time-box">
            <p style="margin: 5px 0; font-size: 0.9rem; color: #8b949e;">Dados dinâmicos do PHP:</p>
            <p style="margin: 5px 0;"><strong>Data:</strong> <?php echo date('d/m/Y'); ?></p>
            <p style="margin: 5px 0;"><strong>Hora do Servidor:</strong> <?php echo date('H:i:s'); ?></p>
        </div>
    </div>

</body>
</html>