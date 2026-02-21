<?php
/**
 * Teste de Conexão com Banco de Dados
 * Use para verificar se tudo está configurado corretamente
 */

echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Teste de Configuração - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 40px; background-color: #f8f9fa; }
        .container { max-width: 800px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: #0468BF; margin-bottom: 30px;">🔧 Teste de Configuração</h1>';

// Teste 1: Conexão com banco
echo '<div class="card mb-3">';
echo '<div class="card-header"><strong>1. Conexão com Banco de Dados</strong></div>';
echo '<div class="card-body">';

try {
    require_once '../../config/database.php';
    $test = obterUmaLinha("SELECT 1 as result");
    if ($test) {
        echo '<span class="badge bg-success">✓ Conexão OK</span>';
    } else {
        echo '<span class="badge bg-danger">✗ Falha na conexão</span>';
    }
} catch (Exception $e) {
    echo '<span class="badge bg-danger">✗ Erro: ' . htmlspecialchars($e->getMessage()) . '</span>';
}
echo '</div></div>';

// Teste 2: Verificar tabelas
echo '<div class="card mb-3">';
echo '<div class="card-header"><strong>2. Tabelas do Banco</strong></div>';
echo '<div class="card-body">';

$tabelas_esperadas = array('usuario', 'pessoa', 'paciente', 'funcionario', 'exame', 'endereco', 'paciente_responsavel', 'pessoa_endereco');
$tabelas_existentes = array();

try {
    foreach ($tabelas_esperadas as $tabela) {
        $sql = "SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
        $result = obterUmaLinha($sql, [DB_NAME, $tabela]);
        
        if ($result) {
            echo '<span class="badge bg-success">' . $tabela . '</span> ';
            $tabelas_existentes[] = $tabela;
        } else {
            echo '<span class="badge bg-warning">' . $tabela . '</span> ';
        }
    }
    echo '<br><br><small class="text-muted">Total: ' . count($tabelas_existentes) . '/' . count($tabelas_esperadas) . ' tabelas</small>';
} catch (Exception $e) {
    echo '<span class="badge bg-danger">Erro ao verificar tabelas</span>';
}
echo '</div></div>';

// Teste 3: Verificar admin
echo '<div class="card mb-3">';
echo '<div class="card-header"><strong>3. Usuário Admin</strong></div>';
echo '<div class="card-body">';

try {
    $admin = obterUmaLinha("SELECT * FROM usuario WHERE perfil = ?", ['ADMIN']);
    if ($admin) {
        echo '<span class="badge bg-success">✓ Admin existe</span><br>';
        echo '<small>Email: ' . htmlspecialchars($admin['email']) . '</small>';
    } else {
        echo '<span class="badge bg-warning">⚠ Nenhum admin encontrado</span><br>';
        echo '<small><a href="init-admin.php">Clique aqui para criar admin</a></small>';
    }
} catch (Exception $e) {
    echo '<span class="badge bg-danger">Erro ao verificar admin</span>';
}
echo '</div></div>';

// Teste 4: Verificar arquivos de configuração
echo '<div class="card mb-3">';
echo '<div class="card-header"><strong>4. Arquivos de Configuração</strong></div>';
echo '<div class="card-body">';

$arquivos = array(
    'config/db.php' => 'Conexão com banco',
    'config/auth.php' => 'Autenticação',
    'config/mailer.php' => 'Email',
    'modulos/painel-usuarios.php' => 'Painel de usuários'
);

foreach ($arquivos as $arquivo => $descricao) {
    if (file_exists($arquivo)) {
        echo '<span class="badge bg-success">✓</span> ' . $arquivo . ' (' . $descricao . ')<br>';
    } else {
        echo '<span class="badge bg-danger">✗</span> ' . $arquivo . ' (' . $descricao . ')<br>';
    }
}
echo '</div></div>';

// Próximas ações
echo '<div class="alert alert-info">';
echo '<strong>✓ Setup Completo!</strong><br><br>';
echo '<strong>Próximas ações:</strong><br>';
echo '1. <a href="init-admin.php">Criar usuário ADMIN</a><br>';
echo '2. <a href="index-login.php">Acessar login</a><br>';
echo '3. <strong>DELETE este arquivo (teste-config.php)</strong> por segurança<br>';
echo '</div>';

echo '    </div>
</body>
</html>';
?>
