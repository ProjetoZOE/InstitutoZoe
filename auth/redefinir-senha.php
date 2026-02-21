<?php
/**
 * REDEFINIR SENHA - VERSÃO MELHORADA
 */

// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redireciona se já autenticado
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../dashboard/admin/index.php');
    exit;
}

// Inclui arquivos necessários
require_once '../config/database.php';

// Variáveis de controle
$mensagem = '';
$tipo_mensagem = '';
$token_valido = false;
$usuario_id = null;
$email = '';

// Verifica o token na URL
if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    
    // Validar formato do token
    if (empty($token) || strlen($token) !== 64) {
        $mensagem = 'Token inválido ou mal formatado.';
        $tipo_mensagem = 'danger';
    } else {
        // Buscar o token no banco de dados
        $sql = "SELECT id_usuario, email FROM usuario 
                WHERE senha_reset_token = ? AND senha_reset_expira > NOW()";
        
        $usuario = obterUmaLinha($sql, [$token]);
        
        if ($usuario) {
            $token_valido = true;
            $usuario_id = $usuario['id_usuario'];
            $email = $usuario['email'];
        } else {
            $mensagem = 'Token inválido ou expirado. <a href="recuperar-senha.php" class="alert-link">Solicitar novo link</a>';
            $tipo_mensagem = 'danger';
        }
    }
} else {
    $mensagem = 'Nenhum token fornecido.';
    $tipo_mensagem = 'danger';
}

// Processa o formulário de redefinição de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_redefinir'])) {
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';
    $token_post = $_POST['token'] ?? '';
    
    // Validações
    if (empty($nova_senha) || empty($confirma_senha)) {
        $mensagem = 'Por favor, preencha todos os campos.';
        $tipo_mensagem = 'danger';
    } else if ($nova_senha !== $confirma_senha) {
        $mensagem = 'As senhas não correspondem.';
        $tipo_mensagem = 'danger';
    } else if (strlen($nova_senha) < 6) {
        $mensagem = 'A senha deve ter no mínimo 6 caracteres.';
        $tipo_mensagem = 'danger';
    } else if (!preg_match('/[A-Z]/', $nova_senha) || !preg_match('/[0-9]/', $nova_senha)) {
        $mensagem = 'A senha deve conter pelo menos uma letra maiúscula e um número.';
        $tipo_mensagem = 'danger';
    } else {
        // Validar token novamente
        $sql_token = "SELECT id_usuario FROM usuario 
                      WHERE senha_reset_token = ? AND senha_reset_expira > NOW()";
        
        $usuario_check = obterUmaLinha($sql_token, [$token_post]);
        
        if (!$usuario_check) {
            $mensagem = 'Token inválido ou expirado. <a href="recuperar-senha.php" class="alert-link">Solicitar novo link</a>';
            $tipo_mensagem = 'danger';
        } else {
            // Criptografar a nova senha
            $senha_hash = password_hash($nova_senha, PASSWORD_BCRYPT);
            
            // Atualizar a senha e limpar os tokens
            $sql_update = "UPDATE usuario 
                           SET senha_hash = ?, senha_reset_token = NULL, senha_reset_expira = NULL 
                           WHERE id_usuario = ?";
            
            if (executarQuery($sql_update, [$senha_hash, $usuario_check['id_usuario']])) {
                $mensagem = 'Senha redefinida com sucesso! <a href="login.php" class="alert-link">Fazer login</a>';
                $tipo_mensagem = 'success';
                $token_valido = false; // Desabilitar formulário
            } else {
                $mensagem = 'Erro ao atualizar a senha. Tente novamente.';
                $tipo_mensagem = 'danger';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Instituto Zoe</title>
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #004ba8;
            --primary-light: #0468BF;
            --bg-gradient: linear-gradient(135deg, #f8faff 0%, #eef2f7 100%);
            --card-shadow: 0 20px 40px rgba(0, 75, 168, 0.08);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: var(--bg-gradient);
            font-family: 'Inter', sans-serif;
            color: #334155;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 28px;
            box-shadow: var(--card-shadow);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .auth-header i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }

        .auth-header h2 {
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .auth-header p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .auth-body {
            padding: 40px 35px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 8px;
            display: block;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 14px;
            padding: 12px 45px 12px 45px;
            border: 2px solid #f1f5f9;
            font-size: 0.95rem;
            transition: var(--transition);
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(4, 104, 191, 0.1);
            background-color: white;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            z-index: 5;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            cursor: pointer;
            z-index: 5;
            background: none;
            border: none;
            padding: 0;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 14px;
            padding: 14px;
            font-weight: 700;
            width: 100%;
            transition: var(--transition);
            margin-top: 10px;
        }

        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 75, 168, 0.2);
        }

        .alert {
            border-radius: 16px;
            border: none;
            padding: 15px 20px;
            font-size: 0.9rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success { background-color: #ecfdf5; color: #065f46; }
        .alert-danger { background-color: #fef2f2; color: #991b1b; }
        .alert-link { font-weight: 700; text-decoration: none; color: inherit; }
        .alert-link:hover { text-decoration: underline; }

        .password-requirements {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: -10px;
            margin-bottom: 20px;
            padding-left: 5px;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-key-fill"></i>
            <h2>Nova Senha</h2>
            <p>Crie uma senha forte para proteger sua conta.</p>
        </div>

        <div class="auth-body">
            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <i class="bi <?php echo ($tipo_mensagem === 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?>"></i>
                    <div><?php echo $mensagem; ?></div>
                </div>
            <?php endif; ?>

            <?php if ($token_valido): ?>
                <form method="POST" action="">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <input type="hidden" name="acao_redefinir" value="1">

                    <div class="form-group">
                        <label class="form-label">Nova Senha</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-lock"></i></span>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="nova_senha" 
                                name="nova_senha" 
                                placeholder="••••••••" 
                                required
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('nova_senha', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmar Nova Senha</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-lock-check"></i></span>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="confirma_senha" 
                                name="confirma_senha" 
                                placeholder="••••••••" 
                                required
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('confirma_senha', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <i class="bi bi-info-circle me-1"></i> Mínimo 6 caracteres, uma letra maiúscula e um número.
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Redefinir Senha <i class="bi bi-check2-circle ms-2"></i>
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center">
                    <a href="login.php" class="btn btn-outline-primary rounded-pill px-4">Ir para o Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php