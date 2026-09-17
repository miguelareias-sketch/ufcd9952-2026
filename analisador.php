<?php
// ==========================================
// 1. CONFIGURAÇÃO DA BASE DE DADOS (SQLite)
// ==========================================
$dbFile = 'helpdesk_simples.db';
$pdo = new PDO("sqlite:$dbFile");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Criar tabela de tickets se não existir
$pdo->exec("CREATE TABLE IF NOT EXISTS tickets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT NOT NULL,
    descricao TEXT NOT NULL,
    prioridade TEXT,
    sentimento TEXT,
    resposta_ia TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ==========================================
// 2. CONFIGURAÇÃO DA API DO GEMINI
// ==========================================
// Cole aqui a sua chave de API para testar. 
// ATENÇÃO: Numa aplicação real, nunca se coloca a chave exposta assim!
$geminiApiKey = getenv('GEMINI_API_KEY');
if (!$geminiApiKey) {
    die("Erro: chave da API Gemini não configurada.");
}
$mensagemSucesso = "";
$mensagemErro = "";

// ==========================================
// 3. PROCESSAMENTO DO FORMULÁRIO (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_ticket'])) {

    //$titulo = $_POST['titulo'] ?? '';
    //$descricao = $_POST['descricao'] ?? '';

    $titulo = trim ($_POST['titulo'] ?? '');
    $descricao = trim ($_POST['descricao'] ?? '');

 //   if (!empty($titulo) && !empty($descricao)) {
 if ($titulo !== '' && $descricao !== '') {       
        $prioridade = "MEDIA";
        $sentimento = "Neutro";
        $respostaSugerida = "Obrigado pelo contacto. Vamos analisar o seu pedido.";

        // Chamada real à API do Gemini se a chave estiver configurada
        if (!empty($geminiApiKey) && $geminiApiKey !== "SUBSTITUA_PELA_SUA_CHAVE_DO_GEMINI") {
            
            $prompt = "Analisa o seguinte ticket de suporte técnico de uma escola e responde estritamente no formato JSON indicado.
            
            Ticket:
            Título: $titulo
            Descrição: $descricao
            
            Formato de resposta JSON esperado:
            {
              \"prioridade\": \"ALTA ou MEDIA ou BAIXA\",
              \"sentimento\": \"Frustrado ou Neutro ou Satisfeito\",
              \"resposta_sugerida\": \"Escreve uma resposta de suporte curta, muito empática e profissional em português.\"
            }";

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $geminiApiKey;
            
            $postData = [
                "contents" => [
                    ["parts" => [["text" => $prompt]]]
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $responseDecoded = json_decode($response, true);
                $aiText = $responseDecoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Limpar possíveis marcações de markdown do JSON da IA
                $aiText = str_replace(['```json', '```'], '', $aiText);
                $aiText = trim($aiText);

                $aiData = json_decode($aiText, true);
                if ($aiData) {
                    $prioridade = $aiData['prioridade'] ?? 'MEDIA';
                    $sentimento = $aiData['sentimento'] ?? 'Neutro';
                    $respostaSugerida = $aiData['resposta_sugerida'] ?? $respostaSugerida;
                }
            }
        }

        // Gravar na base de dados SQLite
        try {
            $stmt = $pdo->prepare("INSERT INTO tickets (titulo, descricao, prioridade, sentimento, resposta_ia) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $descricao, $prioridade, $sentimento, $respostaSugerida]);
            $mensagemSucesso = "Ticket submetido e analisado pela IA com sucesso!";
        } catch (Exception $e) {
            $mensagemErro = "Erro ao gravar na base de dados: " . $e->getMessage();
        }

    } else {
        $mensagemErro = "Por favor, preencha todos os campos.";
    }
}

// Obter todos os tickets para listar na tabela
$tickets = $pdo->query("SELECT * FROM tickets ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Helpdesk Inteligente IA - Protótipo Inicial</title>
    <style>
        body { font-family: sans-serif; background: #0e1117; color: #c9d1d9; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .form-box { background: #161b22; border: 1px solid #30363d; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; background: #21262d; border: 1px solid #30363d; color: white; border-radius: 4px; box-sizing: border-box; }
        button { background: #238636; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #2ea043; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #30363d; padding: 12px; text-align: left; }
        th { background: #21262d; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; }
        .alta { background: #da3637; color: white; }
        .media { background: #d29922; color: black; }
        .baixa { background: #238636; color: white; }
        .alerta { background: #1f6feb; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🎫 Helpdesk Escolar com Triagem IA</h1>
    <p>Módulo de Demonstração Inicial - UFCD 9952</p>

    <?php if ($mensagemSucesso): ?>
        <div class="alerta" style="background: #238636;"><?= $mensagemSucesso ?></div>
    <?php endif; ?>
    <?php if ($mensagemErro): ?>
        <div class="alerta" style="background: #da3637;"><?= $mensagemErro ?></div>
    <?php endif; ?>

    <!-- Formulário de Entrada -->
    <div class="form-box">
        <h2>Abrir Novo Ticket de Suporte</h2>
        <form method="POST">
            <label>Título da Ocorrência:</label>
            <input type="text" name="titulo" placeholder="Ex: Projetor da Sala 5 não liga" required>
            
            <label>Descrição Detalhada do Problema:</label>
            <textarea name="descricao" rows="4" placeholder="Descreva o que aconteceu..." required></textarea>
            
            <button type="submit" name="criar_ticket">Submeter e Analisar com IA 🤖</button>
        </form>
    </div>

    <!-- Tabela de Resultados -->
    <h2>Histórico de Pedidos Analisados</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ticket</th>
                <th>Análise de Sentimento (IA)</th>
                <th>Prioridade Atribuída (IA)</th>
                <th>Sugestão de Resposta da IA</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tickets)): ?>
                <tr><td colspan="5" style="text-align: center;">Nenhum ticket registado ainda.</td></tr>
            <?php else: ?>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($t['titulo']) ?></strong><br>
                            <small style="color: #8b949e;"><?= htmlspecialchars($t['descricao']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($t['sentimento']) ?></td>
                        <td>
                            <span class="badge <?= strtolower($t['prioridade']) ?>">
                                <?= htmlspecialchars($t['prioridade']) ?>
                            </span>
                        </td>
                        <td style="font-style: italic; color: #58a6ff;">
                            "<?= htmlspecialchars($t['resposta_ia']) ?>"
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>