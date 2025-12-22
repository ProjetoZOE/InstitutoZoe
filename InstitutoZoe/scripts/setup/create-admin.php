<?php
/**
 * SCRIPT DE INICIALIZAÇÃO - Use apenas UMA VEZ
 * Cria usuário ADMIN padrão no banco de dados
 * 
 * SEGURANÇA: 
 * - Verificação rigorosa de IP (apenas localhost)
 * - Flag file para evitar múltiplas execuções
 * - DELETE ESTE ARQUIVO APÓS USAR!
 */

// ========================
// PROTEÇÃO DE SEGURANÇA #1: Verificar se já foi executado
// ========================
if (file_exists(__DIR__ . '/config/.admin-criado')) {
    http_response_code(403);
    $data_criacao = file_get_contents(__DIR__ . '/config/.admin-criado');
    die('❌ <strong>ACESSO NEGADO</strong><br><br>' .
        'Admin já foi criado em: ' . htmlspecialchars($data_criacao) . '<br>' .
        'Se deseja criar novamente, delete o arquivo <code>config/.admin-criado</code> e contate o administrador.<br><br>' .
        '<a href="index-login.php">Ir para Login</a>');
}

// ========================
// PROTEÇÃO DE SEGURANÇA #2: Verificação de IP (apenas localhost)
// ========================
$ip_cliente = $_SERVER['REMOTE_ADDR'] ?? '';
$ips_permitidos = array('127.0.0.1', 'localhost', '::1');

if (!in_array($ip_cliente, $ips_permitidos)) {
    http_response_code(403);
    error_log('🚨 TENTATIVA DE ACESSO NÃO AUTORIZADO a init-admin.php de IP: ' . $ip_cliente);
    die('❌ <strong>ACESSO NEGADO</strong><br><br>' .
        'Este script só pode ser acessado do servidor local (localhost).<br>' .
        'Seu IP: ' . htmlspecialchars($ip_cliente) . '<br><br>' .
        '<a href="index-login.php">Voltar</a>');
}

require_once '../../config/database.php';

// ========================
// FORMULÁRIO (GET)
// ========================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Admin - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #0468BF 0%, #F28705 100%); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { 
            max-width: 500px; 
            background: white; 
            padding: 40px; 
            border-radius: 8px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h2 { 
            color: #0468BF; 
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert { 
            margin-bottom: 20px; 
            border-radius: 4px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-top: 15px;
        }
        .btn-primary {
            background: #0468BF;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #034d96;
        }
        small {
            display: block;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="bi bi-shield-check"></i> Criar Admin</h2>
        <hr style="border: 2px solid #F28705; opacity: 1; width: 50px;">
        
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> <strong>PRIMEIRA EXECUÇÃO:</strong><br>
            Este formulário só pode ser acessado <strong>UMA VEZ</strong>. Após criado, o acesso será bloqueado permanentemente.
        </div>
        
        <form method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email do Admin *</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="admin@institutozoe.com.br" required>
            </div>
            
            <div class="mb-3">
                <label for="senha" class="form-label">Senha (mínimo 8 caracteres) *</label>
                <input type="password" class="form-control" id="senha" name="senha" 
                       placeholder="Crie uma senha forte" required minlength="8">
                <small class="text-muted">Use maiúsculas, minúsculas, números e símbolos</small>
            </div>
            
            <div class="mb-3">
                <label for="confirma_senha" class="form-label">Confirmar Senha *</label>
                <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" 
                       placeholder="Confirme a senha" required minlength="8">
            </div>
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo *</label>
                <input type="text" class="form-control" id="nome" name="nome" 
                       value="Administrador" required minlength="3">
            </div>
            
            <div class="alert alert-danger">
                <i class="bi bi-lock-fill"></i> <strong>SEGURANÇA:</strong><br>
                ✓ IP verificado (apenas localhost)<br>
                ✓ Execução única protegida<br>
                ✓ Delete este arquivo após usar
            </div>
            
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-lg"></i> Criar Admin
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
    exit;
}

// ========================
// PROCESSAMENTO DO FORMULÁRIO (POST)
// ========================

$email_admin = trim($_POST['email'] ?? '');
$senha_admin = $_POST['senha'] ?? '';
$confirma_senha = $_POST['confirma_senha'] ?? '';
$nome_admin = trim($_POST['nome'] ?? '');

// Validações
$erros = array();

if (empty($email_admin) || !filter_var($email_admin, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Email inválido.';
}

if (strlen($senha_admin) < 8) {
    $erros[] = 'Senha deve ter no mínimo 8 caracteres.';
}

if ($senha_admin !== $confirma_senha) {
    $erros[] = 'Senhas não conferem.';
}

if (empty($nome_admin) || strlen($nome_admin) < 3) {
    $erros[] = 'Nome deve ter no mínimo 3 caracteres.';
}

// Se houver erros, exibir
if (!empty($erros)) {
    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Erro - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 40px 0; }
        .container { max-width: 500px; background: white; padding: 40px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-danger"><i class="bi bi-exclamation-circle"></i> Erros encontrados</h2>
        <div class="alert alert-danger">
            <ul>
    <?php foreach ($erros as $erro): ?>
                <li><?php echo htmlspecialchars($erro); ?></li>
    <?php endforeach; ?>
            </ul>
        </div>
        <a href="init-admin.php" class="btn btn-secondary">Voltar</a>
    </div>
</body>
</html>
    <?php
    exit;
}

// Verificar se email já existe
$sql_check = "SELECT id_usuario FROM usuario WHERE email = ?";
$usuario_existente = obterUmaLinha($sql_check, [$email_admin]);

if ($usuario_existente) {
    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Erro - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 40px 0; }
        .container { max-width: 500px; background: white; padding: 40px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-danger"><i class="bi bi-exclamation-circle"></i> Email já cadastrado</h2>
        <div class="alert alert-danger">
            Email <strong><?php echo htmlspecialchars($email_admin); ?></strong> já está cadastrado!
        </div>
        <a href="index-login.php" class="btn btn-primary">Ir para Login</a>
    </div>
</body>
</html>
    <?php
    exit;
}

// Criar o admin
try {
    $pdo->beginTransaction();
    
    // Criar usuário ADMIN
    $senha_hash = password_hash($senha_admin, PASSWORD_BCRYPT);
    $sql_usuario = "INSERT INTO usuario (email, senha_hash, perfil, ativo, email_verificado) 
                   VALUES (?, ?, ?, ?, ?)";
    executarQuery($sql_usuario, [$email_admin, $senha_hash, 'ADMIN', 1, 1]);
    
    // Obter ID do usuário
    $id_usuario = $pdo->lastInsertId();
    
    // Criar pessoa associada
    $sql_pessoa = "INSERT INTO pessoa (nome, id_usuario, ativo) 
                  VALUES (?, ?, ?)";
    executarQuery($sql_pessoa, [$nome_admin, $id_usuario, 1]);
    
    // Confirmar transação
    $pdo->commit();
    
    // Marcar como criado (flag file)
    @mkdir(__DIR__ . '/config', 0755, true);
    file_put_contents(__DIR__ . '/config/.admin-criado', date('Y-m-d H:i:s') . ' - ' . $email_admin);
    chmod(__DIR__ . '/config/.admin-criado', 0644);
    
    // Log de auditoria
    error_log('✅ ADMIN CRIADO: ' . $email_admin . ' em ' . date('Y-m-d H:i:s') . ' de IP: ' . $ip_cliente);
    
    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucesso - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0468BF 0%, #F28705 100%); min-height: 100vh; display: flex; align-items: center; }
        .container { max-width: 600px; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        code { background: #f0f0f0; padding: 10px; display: block; margin: 10px 0; border-radius: 4px; word-break: break-all; }
        .success-icon { color: #28a745; font-size: 48px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center">
            <i class="bi bi-check-circle success-icon"></i>
            <h2 class="text-success"><i class="bi bi-check-circle-fill"></i> Admin Criado com Sucesso!</h2>
            <hr style="border: 2px solid #F28705; opacity: 1; width: 50px; margin: 0 auto;">
        </div>
        
        <div class="alert alert-success mt-4">
            <i class="bi bi-check-lg"></i> Usuário ADMIN criado e pronto para usar!
        </div>
        
        <h5 class="mt-4">📋 Credenciais de Acesso:</h5>
        <div class="mb-3">
            <p><strong>Email:</strong></p>
            <code><?php echo htmlspecialchars($email_admin); ?></code>
        </div>
        <div class="mb-3">
            <p><strong>Senha:</strong></p>
            <code>A senha que você definiu</code>
        </div>
        
        <div class="alert alert-warning mt-4">
            <i class="bi bi-exclamation-triangle"></i> <strong>IMPORTANTE:</strong><br>
            1. ✅ Anote as credenciais em local seguro<br>
            2. ✅ Nunca compartilhe estas credenciais<br>
            3. ✅ Altere a senha após o primeiro login<br>
            4. ✅ Este arquivo agora está <strong>permanentemente bloqueado</strong><br>
            5. ✅ <strong>DELETE</strong> o arquivo <code>init-admin.php</code> se possível
        </div>
        
        <div class="text-center mt-4">
            <a href="index-login.php" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-right"></i> Ir para Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <?php
    
} catch (Exception $e) {
    error_log('❌ Erro ao criar admin: ' . $e->getMessage());
    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Erro - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 40px 0; }
        .container { max-width: 500px; background: white; padding: 40px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-danger"><i class="bi bi-exclamation-circle"></i> Erro ao Criar Admin</h2>
        <div class="alert alert-danger">
            Ocorreu um erro ao criar o usuário admin. Verifique se o banco de dados está configurado corretamente.
        </div>
        <a href="index-login.php" class="btn btn-secondary">Voltar</a>
    </div>
</body>
</html>
    <?php
}

?>
