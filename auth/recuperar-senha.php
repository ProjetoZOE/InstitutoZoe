<?php
/**
 * RECUPERAR SENHA - VERSÃO MELHORADA
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
require_once '../config/email.php';

// Variáveis de controle
$mensagem = '';
$tipo_mensagem = '';

// Processa o formulário de recuperação de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    // Validações
    if (empty($email)) {
        $mensagem = 'Por favor, informe seu email.';
        $tipo_mensagem = 'danger';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Email inválido.';
        $tipo_mensagem = 'danger';
    } else {
        // Verifica se o email existe na base de dados
        $sql = "SELECT id_usuario, email FROM usuario WHERE email = ?";
        $usuario = obterUmaLinha($sql, [$email]);
        
        if (!$usuario) {
            error_log('Tentativa de recuperação de senha para email inexistente: ' . $email);
            $mensagem = 'Se este email estiver cadastrado, você receberá um link para redefinir sua senha.';
            $tipo_mensagem = 'success';
        } else {
            // Gerar token único para reset de senha
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', time() + 60 * 60); // Expira em 1 hora
            
            // Salvar token no banco de dados
            $sql_update = "UPDATE usuario SET senha_reset_token = ?, senha_reset_expira = ? WHERE id_usuario = ?";
            
            if (executarQuery($sql_update, [$token, $expira, $usuario['id_usuario']])) {
                // Enviar email de recuperação
                $nome_usuario = obterNomeUsuario($usuario['id_usuario']);
                $enviado = enviarEmailRecuperacaoSenha($email, $nome_usuario, $token);
                
                if ($enviado) {
                    $mensagem = 'Se este email estiver cadastrado, você receberá um link para redefinir sua senha.';
                    $tipo_mensagem = 'success';
                } else {
                    $mensagem = 'Se este email estiver cadastrado, você receberá um link para redefinir sua senha.';
                    $tipo_mensagem = 'success';
                }
            } else {
                $mensagem = 'Erro ao processar sua solicitação. Tente novamente.';
                $tipo_mensagem = 'danger';
            }
        }
    }
}

/**
 * Obtém o nome do usuário pelo ID
 */
function obterNomeUsuario($id_usuario) {
    $sql = "SELECT p.nome FROM pessoa p 
            INNER JOIN usuario u ON p.id_usuario = u.id_usuario 
            WHERE u.id_usuario = ?";
    $result = obterUmaLinha($sql, [$id_usuario]);
    return $result ? $result['nome'] : 'Usuário';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Instituto Zoe</title>
    
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
            margin-bottom: 25px;
        }

        .form-control {
            border-radius: 14px;
            padding: 12px 15px 12px 45px;
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

        .auth-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: #64748b;
        }

        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 700;
        }

        .auth-footer a:hover {
            text-decoration: underline;
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

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container img {
            height: 50px;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-shield-lock"></i>
            <h2>Recuperar Senha</h2>
            <p>Enviaremos um link de redefinição para o seu e-mail cadastrado.</p>
        </div>

        <div class="auth-body">
            <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <i class="bi <?php echo ($tipo_mensagem === 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?>"></i>
                    <div><?php echo $mensagem; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="email">Endereço de E-mail</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="bi bi-envelope"></i></span>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            placeholder="exemplo@email.com" 
                            required
                            autofocus
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Enviar Link de Recuperação <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="auth-footer">
                Lembrou sua senha? <a href="login.php">Voltar ao Login</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
