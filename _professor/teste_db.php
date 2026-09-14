<?php
// 1. Configurações de acesso à Base de Dados no Codespaces
$host = '127.0.0.1';
$db   = 'helpdesk_ia';
$user = 'admin';
$pass = 'password123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // 2. Estabelecer a ligação segura com PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<h1>Ligação ao MySQL: <span style='color: #238636;'>SUCESSO! ✅</span></h1>";

    // 3. Criar a tabela 'tickets' se não existir (baseada na semente original)
    $sqlCriarTabela = "CREATE TABLE IF NOT EXISTS tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        descricao TEXT NOT NULL,
        prioridade VARCHAR(50),
        sentimento VARCHAR(50),
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlCriarTabela);
    echo "<p style='color: #8b949e;'>✓ Tabela 'tickets' verificada/criada na base de dados.</p>";

    // 4. Inserir um registo fictício se a tabela estiver vazia
    $stmtCheck = $pdo->query("SELECT COUNT(*) FROM tickets");
    $total = $stmtCheck->fetchColumn();

    if ($total == 0) {
        $sqlInserir = "INSERT INTO tickets (titulo, descricao, prioridade, sentimento) 
                       VALUES (:titulo, :descricao, :prioridade, :sentimento)";
        $stmtInsert = $pdo->prepare($sqlInserir);
        $stmtInsert->execute([
            'titulo' => 'Falha no acesso ao Wi-Fi da escola',
            'descricao' => 'Os computadores da sala de multimédia não ligam à rede.',
            'prioridade' => 'ALTA',
            'sentimento' => 'Frustrado'
        ]);
        echo "<p style='color: #58a6ff;'>✓ Registo de demonstração criado com sucesso!</p>";
    }

    // 5. Ler e listar os registos existentes para o ecrã
    $stmtListar = $pdo->query("SELECT * FROM tickets");
    $tickets = $stmtListar->fetchAll();

    echo "<h2>Registos Ativos na Base de Dados:</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; background: #161b22; color: #c9d1d9; border-color: #30363d; font-family: sans-serif;'>";
    echo "<tr style='background: #21262d;'><th>ID</th><th>Título</th><th>Descrição</th><th>Prioridade</th><th>Sentimento</th></tr>";
    foreach ($tickets as $ticket) {
        echo "<tr>";
        echo "<td>" . $ticket['id'] . "</td>";
        echo "<td>" . htmlspecialchars($ticket['titulo']) . "</td>";
        echo "<td>" . htmlspecialchars($ticket['descricao']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($ticket['prioridade']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($ticket['sentimento']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (\PDOException $e) {
    echo "<h1>Ligação ao MySQL: <span style='color: #da3637;'>FALHOU! ❌</span></h1>";
    echo "<p style='color: #da3637;'><strong>Erro de ligação:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}