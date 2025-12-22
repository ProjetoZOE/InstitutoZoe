<?php
/**
 * Script de migração - Adiciona colunas de verificação de email
 * Acesso: apenas localhost
 * Execute: http://localhost/InstitutoZoe/migrate-db.php
 */

// Verificar IP
$ip_cliente = $_SERVER['REMOTE_ADDR'] ?? '';
$ips_permitidos = array('127.0.0.1', 'localhost', '::1');

if (!in_array($ip_cliente, $ips_permitidos)) {
    http_response_code(403);
    die('❌ Acesso negado - apenas localhost');
}

require_once '../../config/database.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Migração - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 40px 0; }
        .container { max-width: 600px; background: white; padding: 40px; border-radius: 8px; }
        code { background: #f0f0f0; padding: 10px; display: block; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">🔄 Migração - Verificação de Email</h2>

<?php

$migrações = array(
    "Adicionar coluna email_verificado" => "ALTER TABLE usuario ADD COLUMN IF NOT EXISTS email_verificado TINYINT DEFAULT 0 AFTER ativo",
    "Adicionar coluna email_token" => "ALTER TABLE usuario ADD COLUMN IF NOT EXISTS email_token VARCHAR(255) NULL AFTER email_verificado",
    "Adicionar coluna email_token_expira" => "ALTER TABLE usuario ADD COLUMN IF NOT EXISTS email_token_expira DATETIME NULL AFTER email_token"
);

foreach ($migrações as $nome => $sql) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        echo '<div class="alert alert-success">✅ ' . htmlspecialchars($nome) . '</div>';
    } catch (PDOException $e) {
        // Se o erro é "duplicate column", significa que já existe
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo '<div class="alert alert-info">ℹ️ ' . htmlspecialchars($nome) . ' (já existe)</div>';
        } else {
            echo '<div class="alert alert-danger">❌ ' . htmlspecialchars($nome) . ': ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

?>

        <div class="alert alert-success mt-4">
            <strong>✅ Migração completa!</strong><br>
            A tabela usuário agora suporta verificação de email.
        </div>

        <div class="alert alert-info">
            <strong>ℹ️ Próximos passos:</strong><br>
            1. Delete este arquivo (migrate-db.php)<br>
            2. Configure credenciais SMTP em config/mailer.php<br>
            3. Testar cadastro com verificação de email
        </div>

        <a href="index-login.php" class="btn btn-primary">Ir para Login</a>
    </div>
</body>
</html>
