<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se o usuário já está autenticado, redireciona para o painel
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../dashboard/admin/index.php');
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
        } else if ($usuario['email_verificado'] == 1) {
            $sucesso_reenvio = '✅ Este email já foi verificado! Você pode fazer login normalmente.';
        } else {
            // Gerar novo token
            $novo_token = bin2hex(random_bytes(32));
            $nova_expira = date('Y-m-d H:i:s', time() + 24 * 60 * 60);
            
            $sql_update = "UPDATE usuario SET email_token = ?, email_token_expira = ? WHERE id_usuario = ?";
            if (executarQuery($sql_update, [$novo_token, $nova_expira, $usuario['id_usuario']])) {
                // Enviar email
                $link_verificacao = 'http://' . $_SERVER['HTTP_HOST'] . '/repo/InstitutoZoe/auth/login.php?verificar_email=1&token=' . $novo_token;
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
            // ✅ VERIFICAR SE EMAIL FOI VERIFICADO
            if (!isset($usuario['email_verificado']) || !$usuario['email_verificado']) {
                $erro_login = '❌ Email não verificado. Verifique seu email para ativar a conta.<br><small class="text-muted">Não recebeu? Solicite um novo email.</small>';
            } else {
                // Sucesso - cria a sessão
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_perfil'] = $usuario['perfil'];
                $_SESSION['pessoa_id'] = $usuario['id_pessoa'];
                $_SESSION['usuario_nome'] = $usuario['nome'] ?? 'Usuário';
                $_SESSION['usuario_ativo'] = $usuario['ativo'] ?? 1;
                $_SESSION['email_verificado'] = $usuario['email_verificado'] ?? 0;
                $_SESSION['ultimo_ativo'] = time(); // Iniciar timeout (5 minutos)
                
                // Redireciona para o painel apropriado baseado no perfil
                // ADMIN vai para painel de controle (dashboard/admin/index.php)
                // Demais usuários vão para painel-usuario.php (dashboard/user/index.php)
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
                
                // Gerar token de verificação seguro (64 caracteres)
                $email_token = bin2hex(random_bytes(32)); // 64 caracteres hexadecimais
                $token_expira = date('Y-m-d H:i:s', time() + 24 * 60 * 60); // 24 horas
                
                // Cria o usuário
                $senha_hash = password_hash($senha_cadastro, PASSWORD_BCRYPT);
                
                $sql_usuario = "INSERT INTO usuario (email, senha_hash, perfil, ativo, email_verificado, email_token, email_token_expira) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                executarQuery($sql_usuario, [$email_limpo, $senha_hash, 'PACIENTE', 1, 0, $email_token, $token_expira]);
                
                // Obtém o ID do usuário criado
                $id_usuario = $pdo->lastInsertId();
                
                // Cria a pessoa associada
                $sql_pessoa = "INSERT INTO pessoa (nome, id_usuario, ativo) 
                              VALUES (?, ?, ?)";
                executarQuery($sql_pessoa, [$nome_cadastro, $id_usuario, 1]);
                
                // Confirma a transação
                $pdo->commit();
                
                // Enviar email de verificação
                $link_verificacao = 'http://' . $_SERVER['HTTP_HOST'] . '/repo/InstitutoZoe/auth/login.php?verificar_email=1&token=' . $email_token;
                if (enviarEmailVerificacao($email_limpo, $nome_cadastro, $link_verificacao)) {
                    $mensagem_sucesso = '✅ Cadastro realizado! Verifique seu email para ativar a conta.';
                } else {
                    $mensagem_sucesso = '✅ Cadastro realizado! Um email de confirmação foi enviado (pode levar alguns minutos).';
                }
                $aba_ativa = 'login';
                
                // Limpa os campos do formulário
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #fff;
        }
        main {
            padding: 0;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 80px;
        }
        .login-section {
            padding: 40px 0;
            background-color: #f8f9fa;
            width: 100%;
            text-align: center;
        }
        .login-form {
            max-width: 400px;
            margin: 0 auto;
        }
        .form-input {
            margin: 10px 0;
            padding: 8px;
            border: 2px solid #004ba8;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
        }
        .form-button {
            background-color: #004ba8;
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-button:hover {
            background-color: #003580;
        }
        .tab-button {
            background: none;
            border: none;
            color: #004ba8;
            font-size: 18px;
            margin: 0 10px;
            cursor: pointer;
        }
        .tab-button.active {
            font-weight: bold;
            text-decoration: underline;
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
            display: none;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <header>
        <?php include '../includes/navbar.php'; ?>
    </header>
    <main>
        <section class="login-section">
            <div class="container">
                <h1 class="text-center" style="color: #004ba8; font-size: 32px; margin-bottom: 20px;">Acesse ou Cadastre-se</h1>
                <p class="text-center" style="color: #333; font-size: 18px; margin-bottom: 20px;">Gerencie sua conta Instituto Zoe.</p>

                <div class="mb-4">
                    <button class="tab-button <?php echo $aba_ativa === 'login' ? 'active' : ''; ?>" onclick="showTab('login')">Login</button>
                    <button class="tab-button <?php echo $aba_ativa === 'cadastro' ? 'active' : ''; ?>" onclick="showTab('cadastro')">Cadastro</button>
                </div>

                <?php if (isset($_GET['sessao_expirada'])): ?>
                    <div class="alert alert-warning text-center" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Sessão expirada!</strong> Sua sessão foi encerrada por inatividade. Faça login novamente.
                    </div>
                <?php endif; ?>

                <?php if (!empty($mensagem_sucesso)): ?>
                    <div class="alert alert-success text-center" role="alert">
                        <i class="bi bi-check-circle"></i> <?php echo $mensagem_sucesso; ?>
                    </div>
                <?php endif; ?>

                <!-- Form de Login -->
                <div id="login-form" class="login-form" style="display: <?php echo $aba_ativa === 'login' ? 'block' : 'none'; ?>;">
                    <?php if (!empty($erro_login)): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <i class="bi bi-exclamation-circle"></i> <?php echo $erro_login; ?>
                            
                            <?php if (strpos($erro_login, 'Email não verificado') !== false): ?>
                                <hr>
                                <p class="mt-2 mb-0">
                                    <button type="button" class="btn btn-sm btn-link" onclick="document.getElementById('reenvio-form').style.display = 'block'; this.parentElement.style.display = 'none';">
                                        🔄 Reenviar email de verificação
                                    </button>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <input type="hidden" name="acao_login" value="1">
                        <div class="mb-3">
                            <label for="login-email" class="form-label" style="color: #004ba8; font-weight: bold;">E-mail:</label>
                            <input type="email" class="form-input" id="login-email" name="login-email" required>
                        </div>
                        <div class="mb-3">
                            <label for="login-password" class="form-label" style="color: #004ba8; font-weight: bold;">Senha:</label>
                            <input type="password" class="form-input" id="login-password" name="login-password" required>
                        </div>
                        <button type="submit" class="form-button">Entrar</button>
                    </form>
                </div>

                <!-- Form de Reenvio de Email -->
                <div id="reenvio-form" class="login-form" style="display: none; margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid #0468BF;">
                    <h5 style="color: #0468BF; margin-bottom: 15px;">📧 Reenviar Email de Verificação</h5>
                    
                    <?php if (!empty($sucesso_reenvio)): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> <?php echo $sucesso_reenvio; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($erro_reenvio)): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i> <?php echo $erro_reenvio; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="acao_reenviar_email" value="1">
                        <div class="mb-3">
                            <label for="email_reenvio" class="form-label">Digite seu email:</label>
                            <input type="email" class="form-input" id="email_reenvio" name="email_reenvio" required placeholder="seu-email@exemplo.com">
                        </div>
                        <button type="submit" class="form-button w-100">Reenviar Email</button>
                        <button type="button" class="btn btn-link w-100 mt-2" onclick="document.getElementById('reenvio-form').style.display = 'none'; document.querySelector('.alert.alert-danger').style.display = 'block';">
                            Cancelar
                        </button>
                    </form>
                    
                    <hr style="margin: 15px 0;">
                    <small class="text-muted">
                        <strong>💡 Dica:</strong> Se não recebeu o email, verifique a pasta de spam.<br>
                        <strong>⏰ Validade:</strong> O link é válido por 24 horas.
                    </small>
                </div>

                <!-- Form de Cadastro -->
                <div id="cadastro-form" class="login-form" style="display: <?php echo $aba_ativa === 'cadastro' ? 'block' : 'none'; ?>;">
                    <?php if (!empty($erro_cadastro)): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($erro_cadastro); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="" id="cadastro-form-submit">
                        <input type="hidden" name="acao_cadastro" value="1">
                        <div class="mb-3">
                            <label for="cadastro-nome" class="form-label" style="color: #004ba8; font-weight: bold;">Nome Completo:</label>
                            <input type="text" class="form-input" id="cadastro-nome" name="cadastro-nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="cadastro-email" class="form-label" style="color: #004ba8; font-weight: bold;">E-mail:</label>
                            <input type="email" class="form-input" id="cadastro-email" name="cadastro-email" required>
                        </div>
                        <div class="mb-3">
                            <label for="cadastro-password" class="form-label" style="color: #004ba8; font-weight: bold;">Senha (mínimo 6 caracteres):</label>
                            <input type="password" class="form-input" id="cadastro-password" name="cadastro-password" required>
                        </div>
                        <div class="mb-3">
                            <label for="cadastro-confirm-password" class="form-label" style="color: #004ba8; font-weight: bold;">Confirmar Senha:</label>
                            <input type="password" class="form-input" id="cadastro-confirm-password" name="cadastro-confirm-password" required>
                            <div id="password-error" class="error-message">As senhas não coincidem.</div>
                        </div>
                        <button type="submit" class="form-button" id="cadastro-button">Cadastrar</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script src="../assets/js/navbar.js"></script>
    <script>
       
        function showTab(tab) {
            if (tab === 'login') {
                document.getElementById('login-form').style.display = 'block';
                document.getElementById('cadastro-form').style.display = 'none';
                document.querySelector('.tab-button[onclick="showTab(\'login\')"]').classList.add('active');
                document.querySelector('.tab-button[onclick="showTab(\'cadastro\')"]').classList.remove('active');
            } else {
                document.getElementById('login-form').style.display = 'none';
                document.getElementById('cadastro-form').style.display = 'block';
                document.querySelector('.tab-button[onclick="showTab(\'cadastro\')"]').classList.add('active');
                document.querySelector('.tab-button[onclick="showTab(\'login\')"]').classList.remove('active');
            }
        }

       
        document.getElementById('cadastro-confirm-password').addEventListener('input', function() {
            const password = document.getElementById('cadastro-password').value;
            const confirmPassword = this.value;
            const errorMessage = document.getElementById('password-error');
            const button = document.getElementById('cadastro-button');

            if (password !== confirmPassword && confirmPassword) {
                errorMessage.style.display = 'block';
                button.disabled = true;
            } else {
                errorMessage.style.display = 'none';
                button.disabled = false;
            }
        });
    </script>
</body>

</html>