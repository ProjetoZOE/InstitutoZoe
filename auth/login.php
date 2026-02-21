<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se o usuário já está autenticado, redireciona para o painel
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['usuario_perfil'] === 'ADMIN') {
        header('Location: ../dashboard/admin/index.php');
    } else {
        header('Location: ../dashboard/user/index.php');
    }
    exit;
}


// Inclui o arquivo de configuração do banco de dados
require_once '../config/database.php';
require_once '../config/email.php';

// Variáveis de controle
$erro_login = '';
$erro_cadastro = '';
$mensagem_sucesso = '';
$aba_ativa = isset($_GET['tab']) ? $_GET['tab'] : 'login';

// ========================
// REENVIAR EMAIL DE VERIFICAÇÃO
// ========================
if (isset($_POST['acao_reenviar_email'])) {
    $email_reenvio = trim($_POST['email_reenvio'] ?? '');
    
    if (empty($email_reenvio) || !filter_var($email_reenvio, FILTER_VALIDATE_EMAIL)) {
        $erro_reenvio = '❌ Email inválido.';
    } else {
        $sql_check = "SELECT id_usuario, email FROM usuario WHERE email = ?";
        $usuario = obterUmaLinha($sql_check, [$email_reenvio]);
        
        if (!$usuario) {
            $erro_reenvio = '❌ Email não encontrado no sistema.';
        } else if (isset($usuario['email_verificado']) && $usuario['email_verificado'] == 1) {
            $sucesso_reenvio = '✅ Este email já foi verificado! Você pode fazer login normalmente.';
        } else {
            // Gerar novo token
            $novo_token = bin2hex(random_bytes(32));
            $nova_expira = date('Y-m-d H:i:s', time() + 24 * 60 * 60);
            
            $sql_update = "UPDATE usuario SET email_token = ?, email_token_expira = ? WHERE id_usuario = ?";
            if (executarQuery($sql_update, [$novo_token, $nova_expira, $usuario['id_usuario']])) {
                // Enviar email
                $link_verificacao = 'http://' . $_SERVER['HTTP_HOST'] . '/public_html/auth/login.php?verificar_email=1&token=' . $novo_token;
                enviarEmailVerificacao($email_reenvio, $usuario['email'], $link_verificacao);
                
                $sucesso_reenvio = '✅ Novo email de verificação enviado! Verifique sua caixa de entrada (e spam).';
            } else {
                $erro_reenvio = '❌ Erro ao reenviar. Tente novamente.';
            }
        }
    }
}

// ========================
// VERIFICAÇÃO DE EMAIL
// ========================
if (isset($_GET['verificar_email']) && isset($_GET['token'])) {
    $token = trim($_GET['token']);
    
    // Validar token
    if (empty($token) || strlen($token) !== 64) {
        $erro_login = '❌ Token inválido ou mal formatado.';
    } else {
        $sql_check = "SELECT id_usuario, email FROM usuario WHERE email_token = ? AND email_token_expira > NOW()";
        $usuario = obterUmaLinha($sql_check, [$token]);
        
        if ($usuario) {
            // Marcar email como verificado
            $sql_update = "UPDATE usuario SET email_verificado = 1, email_token = NULL, email_token_expira = NULL WHERE id_usuario = ?";
            if (executarQuery($sql_update, [$usuario['id_usuario']])) {
                $mensagem_sucesso = '✅ Email verificado com sucesso! Você já pode fazer login.';
                $aba_ativa = 'login';
            } else {
                $erro_login = '❌ Erro ao verificar email. Tente novamente.';
            }
        } else {
            $erro_login = '❌ Token inválido ou expirado. Solicite um novo email de confirmação.';
        }
    }
}

// Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_login'])) {
    $email_login = trim($_POST['login-email'] ?? '');
    $senha_login = $_POST['login-password'] ?? '';
    
    // Validações básicas
    if (empty($email_login) || empty($senha_login)) {
        $erro_login = 'Por favor, preencha todos os campos.';
    } else if (!filter_var($email_login, FILTER_VALIDATE_EMAIL)) {
        $erro_login = 'Email inválido.';
    } else {
        // Busca o usuário no banco
        $sql = "SELECT u.*, p.id_pessoa, p.nome FROM usuario u 
                LEFT JOIN pessoa p ON u.id_usuario = p.id_usuario 
                WHERE u.email = ? AND u.ativo = 1";
        
        $usuario = obterUmaLinha($sql, [$email_login]);
        
        if ($usuario && password_verify($senha_login, $usuario['senha_hash'])) {
            // ✅ VERIFICAR SE EMAIL FOI VERIFICADO (se a coluna existir)
            if (isset($usuario['email_verificado']) && !$usuario['email_verificado']) {
                $erro_login = '❌ Email não verificado. Verifique seu email para ativar a conta.';
            } else {
                // Sucesso - cria a sessão
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_perfil'] = $usuario['perfil'];
                $_SESSION['pessoa_id'] = $usuario['id_pessoa'] ?? null;
                $_SESSION['usuario_nome'] = $usuario['nome'] ?? 'Usuário';
                $_SESSION['usuario_ativo'] = $usuario['ativo'] ?? 1;
                $_SESSION['email_verificado'] = $usuario['email_verificado'] ?? 1;
                $_SESSION['ultimo_ativo'] = time();
                
                // Redireciona baseado no perfil
                if ($usuario['perfil'] === 'ADMIN') {
                    header('Location: ../dashboard/admin/index.php');
                } else {
                    header('Location: ../dashboard/user/index.php');
                }
                exit;
            }
        } else {
            $erro_login = 'Email ou senha inválidos, ou a conta está inativa.';
        }
    }
}

// Processa o formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_cadastro'])) {
    $nome_cadastro = trim($_POST['cadastro-nome'] ?? '');
    $email_cadastro = trim($_POST['cadastro-email'] ?? '');
    $email_limpo = strtolower($email_cadastro);
    $senha_cadastro = $_POST['cadastro-password'] ?? '';
    $confirma_senha = $_POST['cadastro-confirm-password'] ?? '';
    
    // Validações
    if (empty($nome_cadastro) || empty($email_cadastro) || empty($senha_cadastro) || empty($confirma_senha)) {
        $erro_cadastro = 'Por favor, preencha todos os campos.';
    } else if (!filter_var($email_cadastro, FILTER_VALIDATE_EMAIL)) {
        $erro_cadastro = 'Email inválido.';
    } else if ($senha_cadastro !== $confirma_senha) {
        $erro_cadastro = 'As senhas não correspondem.';
    } else if (strlen($senha_cadastro) < 6) {
        $erro_cadastro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        // Verifica se o email já existe
        $sql_verificar = "SELECT id_usuario FROM usuario WHERE email = ?";
        $usuario_existente = obterUmaLinha($sql_verificar, [$email_limpo]);
        
        if ($usuario_existente) {
            $erro_cadastro = 'Este email já está cadastrado.';
        } else {
            try {
                // Inicia uma transação
                $pdo->beginTransaction();
                
                // Gerar token de verificação seguro (se a coluna existir)
                $email_token = bin2hex(random_bytes(32));
                $token_expira = date('Y-m-d H:i:s', time() + 24 * 60 * 60);
                
                // Cria o usuário (verifica se as colunas de email_verificado existem)
                $senha_hash = password_hash($senha_cadastro, PASSWORD_BCRYPT);
                
                // Tenta inserir com verificação de email, se falhar usa estrutura básica
                try {
                    $sql_usuario = "INSERT INTO usuario (email, senha_hash, perfil, ativo, email_verificado, email_token, email_token_expira) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)";
                    executarQuery($sql_usuario, [$email_limpo, $senha_hash, 'PACIENTE', 1, 0, $email_token, $token_expira]);
                } catch (Exception $e) {
                    // Se falhar (colunas não existem), usa estrutura básica
                    $sql_usuario = "INSERT INTO usuario (email, senha_hash, perfil, ativo) VALUES (?, ?, ?, ?)";
                    executarQuery($sql_usuario, [$email_limpo, $senha_hash, 'PACIENTE', 1]);
                }
                
                // Obtém o ID do usuário criado
                $id_usuario = $pdo->lastInsertId();
                
                // Cria a pessoa associada com data de nascimento padrão
                $data_nascimento = date('Y-m-d'); // ou solicitar no formulário
                $sql_pessoa = "INSERT INTO pessoa (nome, data_nascimento, id_usuario, ativo) 
                              VALUES (?, ?, ?, ?)";
                executarQuery($sql_pessoa, [$nome_cadastro, $data_nascimento, $id_usuario, 1]);
                
                // Confirma a transação
                $pdo->commit();
                
                // Tentar enviar email de verificação se a função existir
                if (function_exists('enviarEmailVerificacao')) {
                    $link_verificacao = 'http://' . $_SERVER['HTTP_HOST'] . '/public_html/auth/login.php?verificar_email=1&token=' . $email_token;
                    enviarEmailVerificacao($email_limpo, $nome_cadastro, $link_verificacao);
                    $mensagem_sucesso = '✅ Cadastro realizado! Verifique seu email para ativar a conta.';
                } else {
                    $mensagem_sucesso = '✅ Cadastro realizado com sucesso! Você já pode fazer login.';
                }
                
                $aba_ativa = 'login';
                $_POST = array();
                
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log('Erro ao cadastrar: ' . $e->getMessage());
                $erro_cadastro = 'Erro ao realizar cadastro. Tente novamente.';
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
    <link rel="shortcut icon" href="../assets/images/logo-Icone.png" type="image/x-icon">
    <title>Instituto Zoe - Login e Cadastro</title>
    
    <!-- Google Fonts - Inter (Importação Prioritária) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        /* CORREÇÃO DA FONTE INTER SEM !IMPORTANT */
        html, body, body * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8f0fe 100%);
            min-height: 100vh;
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        main {
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 100px;
            margin-bottom: 40px;
            padding: 40px 20px;
        }

        .login-container {
            width: 100%;
            max-width: 1000px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 75, 168, 0.15);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-left {
            background: linear-gradient(135deg, #004ba8 0%, #0468BF 100%);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-20px, 20px) rotate(180deg); }
        }

        .login-left-content {
            position: relative;
            z-index: 1;
        }

        .login-left h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .login-left p {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .feature-list {
            list-style: none;
            margin-top: 30px;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .feature-list li i {
            margin-right: 12px;
            font-size: 20px;
            color: #90caf9;
        }

        .login-right {
            padding: 60px 50px;
            background: white;
        }

        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 40px;
            background: #f5f7fa;
            padding: 6px;
            border-radius: 12px;
        }

        .tab-button {
            flex: 1;
            background: transparent;
            border: none;
            color: #666;
            font-size: 16px;
            font-weight: 600;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .tab-button:hover {
            color: #004ba8;
        }

        .tab-button.active {
            background: white;
            color: #004ba8;
            box-shadow: 0 2px 8px rgba(0, 75, 168, 0.1);
        }

        .form-container {
            display: none;
            animation: fadeIn 0.4s ease-out;
        }

        .form-container.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            color: #333;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border: 2px solid #e0e7ff;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-input:focus {
            outline: none;
            border-color: #004ba8;
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 75, 168, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #004ba8;
            font-size: 18px;
            pointer-events: none;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #004ba8;
        }

        .form-button {
            width: 100%;
            background: linear-gradient(135deg, #004ba8 0%, #0468BF 100%);
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 75, 168, 0.2);
        }

        .form-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 75, 168, 0.3);
        }

        .form-button:active {
            transform: translateY(0);
        }

        .form-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .alert i {
            font-size: 20px;
        }

        .password-strength {
            height: 4px;
            background: #e0e7ff;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .password-strength-weak {
            width: 33%;
            background: #dc3545;
        }

        .password-strength-medium {
            width: 66%;
            background: #ffc107;
        }

        .password-strength-strong {
            width: 100%;
            background: #28a745;
        }

        .reenvio-container {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 4px solid #0468BF;
            display: none;
        }

        .reenvio-container.active {
            display: block;
            animation: fadeIn 0.4s ease-out;
        }

        .reenvio-container h5 {
            color: #0468BF;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }

        .btn-link {
            background: none;
            border: none;
            color: #0468BF;
            text-decoration: underline;
            cursor: pointer;
            font-size: 14px;
            padding: 0;
        }

        .btn-link:hover {
            color: #034a8d;
        }

        .divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e7ff;
        }

        .divider span {
            position: relative;
            background: white;
            padding: 0 16px;
            color: #666;
            font-size: 14px;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .login-left {
                padding: 40px 30px;
            }

            .login-left h2 {
                font-size: 24px;
            }

            .login-right {
                padding: 40px 30px;
            }

            main {
                margin-top: 80px;
                padding: 20px 10px;
            }
        }

        @media (max-width: 480px) {
            .login-right {
                padding: 30px 20px;
            }

            .form-input {
                padding: 12px 14px 12px 40px;
                font-size: 14px;
            }

            .tab-button {
                font-size: 14px;
                padding: 10px 16px;
            }
        }
    </style>
</head>

<body>
    <header>
        <?php include '../includes/navbar.php'; ?>
    </header>

    <main>
        <div class="login-container">
            <!-- Lado Esquerdo - Informações -->
            <div class="login-left">
                <div class="login-left-content">
                    <h2>Bem-vindo ao Instituto Zoe</h2>
                    <p>Gerencie suas atividades, consultas e acompanhamentos de forma simples e segura.</p>
                    
                    <ul class="feature-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Acesse seu histórico médico</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Agende consultas e atividades</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Acompanhe seus exames</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Conecte-se com nossos profissionais</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Lado Direito - Formulários -->
            <div class="login-right">
                <!-- Mensagens de Sistema -->
                <?php if (isset($_GET['sessao_expirada'])): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span><strong>Sessão expirada!</strong> Sua sessão foi encerrada por inatividade. Faça login novamente.</span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($mensagem_sucesso)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><?php echo $mensagem_sucesso; ?></span>
                    </div>
                <?php endif; ?>

                <!-- Botões de Abas -->
                <div class="tab-buttons">
                    <button class="tab-button <?php echo $aba_ativa === 'login' ? 'active' : ''; ?>" 
                            onclick="showTab('login')" id="login-tab">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                    <button class="tab-button <?php echo $aba_ativa === 'cadastro' ? 'active' : ''; ?>" 
                            onclick="showTab('cadastro')" id="cadastro-tab">
                        <i class="bi bi-person-plus"></i> Cadastro
                    </button>
                </div>

                <!-- Formulário de Login -->
                <div id="login-form" class="form-container <?php echo $aba_ativa === 'login' ? 'active' : ''; ?>">
                    <?php if (!empty($erro_login)): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <div>
                                <div><?php echo $erro_login; ?></div>
                                <?php if (strpos($erro_login, 'Email não verificado') !== false): ?>
                                    <button type="button" class="btn-link mt-2" onclick="toggleReenvio()">
                                        🔄 Reenviar email de verificação
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="acao_login" value="1">
                        
                        <div class="form-group">
                            <label for="login-email" class="form-label">
                                <i class="bi bi-envelope"></i> E-mail
                            </label>
                            <div class="form-input-wrapper">
                                <i class="bi bi-envelope-fill input-icon"></i>
                                <input type="email" 
                                       class="form-input" 
                                       id="login-email" 
                                       name="login-email" 
                                       placeholder="seu@email.com"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="login-password" class="form-label">
                                <i class="bi bi-lock"></i> Senha
                            </label>
                            <div class="form-input-wrapper">
                                <i class="bi bi-lock-fill input-icon"></i>
                                <input type="password" 
                                       class="form-input" 
                                       id="login-password" 
                                       name="login-password" 
                                       placeholder="••••••••"
                                       required>
                                <i class="bi bi-eye password-toggle" 
                                   onclick="togglePassword('login-password', this)"></i>
                            </div>
                        </div>

                        <button type="submit" class="form-button">
                            <i class="bi bi-box-arrow-in-right"></i> Entrar
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <small>
                            <a href="recuperar-senha.php" style="color: #0468BF; text-decoration: none; font-weight: 600;">
                                <i class="bi bi-key"></i> Esqueci minha senha
                            </a>
                        </small>
                    </div>

                    <!-- Container de Reenvio de Email -->
                    <div id="reenvio-container" class="reenvio-container">
                        <h5>Reenviar Verificação</h5>
                        <p class="small text-muted mb-3">Informe seu email para receber um novo link de ativação.</p>
                        <form method="POST" action="">
                            <input type="hidden" name="acao_reenviar_email" value="1">
                            <div class="form-group mb-3">
                                <input type="email" name="email_reenvio" class="form-input" placeholder="seu@email.com" required>
                            </div>
                            <button type="submit" class="form-button py-2">Enviar Link</button>
                        </form>
                    </div>
                </div>

                <!-- Formulário de Cadastro -->
                <div id="cadastro-form" class="form-container <?php echo $aba_ativa === 'cadastro' ? 'active' : ''; ?>">
                    <?php if (!empty($erro_cadastro)): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span><?php echo $erro_cadastro; ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" id="form-cadastro">
                        <input type="hidden" name="acao_cadastro" value="1">
                        
                        <div class="form-group">
                            <label for="cadastro-nome" class="form-label">
                                <i class="bi bi-person"></i> Nome Completo
                            </label>
                            <div class="form-input-wrapper">
                                <i class="bi bi-person-fill input-icon"></i>
                                <input type="text" 
                                       class="form-input" 
                                       id="cadastro-nome" 
                                       name="cadastro-nome" 
                                       placeholder="Seu nome completo"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cadastro-email" class="form-label">
                                <i class="bi bi-envelope"></i> E-mail
                            </label>
                            <div class="form-input-wrapper">
                                <i class="bi bi-envelope-fill input-icon"></i>
                                <input type="email" 
                                       class="form-input" 
                                       id="cadastro-email" 
                                       name="cadastro-email" 
                                       placeholder="seu@email.com"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cadastro-password" class="form-label">
                                <i class="bi bi-lock"></i> Senha
                            </label>
                            <div class="form-input-wrapper">
                                <i class="bi bi-lock-fill input-icon"></i>
                                <input type="password" 
                                       class="form-input" 
                                       id="cadastro-password" 
                                       name="cadastro-password" 
                                       placeholder="Mínimo 6 caracteres"
                                       onkeyup="checkPasswordStrength(this.value)"
                                       required>
                                <i class="bi bi-eye password-toggle" 
                                   onclick="togglePassword('cadastro-password', this)"></i>
                            </div>
                            <div class="password-strength">
                                <div id="strength-bar" class="password-strength-bar"></div>
                            </div>
                            <small id="strength-text" class="text-muted" style="font-size: 11px;"></small>
                        </div>

                        <div class="form-group">
                            <label for="cadastro-confirm-password" class="form-label">
                                <i class="bi bi-lock-check"></i> Confirmar Senha
                            </label>
                            <div class="form-input-wrapper">
                                <i class="bi bi-lock-fill input-icon"></i>
                                <input type="password" 
                                       class="form-input" 
                                       id="cadastro-confirm-password" 
                                       name="cadastro-confirm-password" 
                                       placeholder="Repita sua senha"
                                       required>
                            </div>
                        </div>

                        <button type="submit" class="form-button">
                            <i class="bi bi-person-plus"></i> Criar Conta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function showTab(tab) {
            // Esconde todos os formulários
            document.querySelectorAll('.form-container').forEach(f => f.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            
            // Mostra o selecionado
            document.getElementById(tab + '-form').classList.add('active');
            document.getElementById(tab + '-tab').classList.add('active');
            
            // Atualiza URL sem recarregar
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);
        }

        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        function toggleReenvio() {
            document.getElementById('reenvio-container').classList.toggle('active');
        }

        function checkPasswordStrength(password) {
            const bar = document.getElementById('strength-bar');
            const text = document.getElementById('strength-text');
            
            if (password.length === 0) {
                bar.className = 'password-strength-bar';
                text.innerText = '';
                return;
            }

            if (password.length < 6) {
                bar.className = 'password-strength-bar password-strength-weak';
                text.innerText = 'Senha muito curta';
                text.style.color = '#dc3545';
            } else if (password.match(/[A-Z]/) && password.match(/[0-9]/) && password.match(/[^A-Za-z0-9]/)) {
                bar.className = 'password-strength-bar password-strength-strong';
                text.innerText = 'Senha forte';
                text.style.color = '#28a745';
            } else {
                bar.className = 'password-strength-bar password-strength-medium';
                text.innerText = 'Senha média (adicione maiúsculas e números)';
                text.style.color = '#ffc107';
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
