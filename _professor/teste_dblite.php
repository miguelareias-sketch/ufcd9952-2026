<?php
// Ligação ao SQLite - Cria um ficheiro local 'base_de_dados.db' automaticamente!
$dsn = "sqlite:base_de_dados.db";

try {
    // 1. Estabelecer a ligação PDO (o driver SQLite vem sempre ativo por padrão no PHP)
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Ligação ao SQL (via SQLite): <span style='color: #238636;'>SUCESSO! ✅</span></h1>";
    echo "<p>O motor de base de dados do PHP está a funcionar perfeitamente.</p>";

    // 2. Criar a tabela 'tickets' (exatamente igual ao MySQL)
    $sqlCriarTabela = "CREATE TABLE IF NOT EXISTS tickets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT NOT NULL,
        descricao TEXT NOT NULL,
        prioridade TEXT,
        sentimento TEXT
    )";
    $pdo->exec($sqlCriarTabela);
    echo "<p style='color: #8b949e;'>✓ Tabela 'tickets' criada/verificada no ficheiro local.</p>";

    // 3. Inserir um registo de teste se a tabela estiver vazia
    $stmtCheck = $pdo->query("SELECT COUNT(*) FROM tickets");
    $total = $stmtCheck->fetchColumn();

    if ($total == 0) {
        $sqlInserir = "INSERT INTO tickets (titulo, descricao, prioridade, sentimento) 
                       VALUES (:titulo, :descricao, :prioridade, :sentimento)";
        $stmtInsert = $pdo->prepare($sqlInserir);
        $stmtInsert->execute([
            'titulo' => 'Dificuldade com Drivers no Codespaces',
            'descricao' => 'O professor resolveu o erro usando SQLite temporariamente.',
            'prioridade' => 'MEDIA',
            'sentimento' => 'Aliviado'
        ]);
        echo "<p style='color: #58a6ff;'>✓ Registo de demonstração guardado com sucesso!</p>";
    }

    // 4. Ler e listar os dados
    $stmtListar = $pdo->query("SELECT * FROM tickets");
    $tickets = $stmtListar->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Registos Ativos:</h2>";
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
    echo "<h1>Erro na Base de Dados: <span style='color: #da3637;'>FALHOU! ❌</span></h1>";
    echo "<p style='color: #da3637;'><strong>Erro:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}