<?php
/**
 * Edição de Perfil do Usuário Autenticado
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticação
require_once '../../config/auth.php';
require_once '../../config/database.php';

verificarAutenticacao();

$usuario = obterDadosUsuario();
$mensagem_sucesso = '';
$erro_mensagem = '';

// Obter dados completos do usuário
$sql = "SELECT u.id_usuario, u.email, p.* FROM usuario u
        LEFT JOIN pessoa p ON u.id_usuario = p.id_usuario
        WHERE u.id_usuario = ?";
$dados_usuario = obterUmaLinha($sql, [$usuario['id']]);

// Processar atualização de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_atualizar'])) {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $rg = trim($_POST['rg'] ?? '');
    $data_nascimento = trim($_POST['data_nascimento'] ?? '');
    $celular = trim($_POST['celular'] ?? '');
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';
    
    // Validar campos obrigatórios
    if (empty($nome)) {
        $erro_mensagem = 'Nome é obrigatório.';
    } else if (!empty($nova_senha)) {
        // Se está tentando mudar senha, validar
        if (empty($senha_atual)) {
            $erro_mensagem = 'Digite sua senha atual para fazer alterações.';
        } else if ($nova_senha !== $confirma_senha) {
            $erro_mensagem = 'As novas senhas não correspondem.';
        } else if (strlen($nova_senha) < 6) {
            $erro_mensagem = 'A nova senha deve ter no mínimo 6 caracteres.';
        } else {
            // Verificar se a senha atual está correta
            $sql_check = "SELECT senha_hash FROM usuario WHERE id_usuario = ?";
            $user_check = obterUmaLinha($sql_check, [$usuario['id']]);
            
            if (!$user_check || !password_verify($senha_atual, $user_check['senha_hash'])) {
                $erro_mensagem = 'Senha atual incorreta.';
            }
        }
    }
    
    // Se não há erro, executar atualização
    if (empty($erro_mensagem)) {
        try {
            $pdo->beginTransaction();
            
            // Atualizar dados da pessoa
            if (!empty($dados_usuario['id_pessoa'])) {
                $sql_pessoa = "UPDATE pessoa SET nome = ?, cpf = ?, rg = ?, data_nascimento = ?, celular = ? 
                              WHERE id_pessoa = ?";
                executarQuery($sql_pessoa, [$nome, $cpf, $rg, $data_nascimento, $celular, $dados_usuario['id_pessoa']]);
            } else {
                // Criar pessoa se não existir
                $sql_pessoa = "INSERT INTO pessoa (nome, cpf, rg, data_nascimento, celular, id_usuario, ativo) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)";
                executarQuery($sql_pessoa, [$nome, $cpf, $rg, $data_nascimento, $celular, $usuario['id'], 1]);
            }
            
            // Atualizar senha se fornecida
            if (!empty($nova_senha)) {
                $senha_hash = password_hash($nova_senha, PASSWORD_BCRYPT);
                $sql_senha = "UPDATE usuario SET senha_hash = ? WHERE id_usuario = ?";
                executarQuery($sql_senha, [$senha_hash, $usuario['id']]);
            }
            
            $pdo->commit();
            
            // Atualizar sessão com novo nome
            $_SESSION['usuario_nome'] = $nome;
            
            $mensagem_sucesso = 'Perfil atualizado com sucesso!';
            
            // Recarregar dados
            $dados_usuario = obterUmaLinha($sql, [$usuario['id']]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('Erro ao atualizar perfil: ' . $e->getMessage());
            $erro_mensagem = 'Erro ao atualizar perfil. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../assets/images/logo-Icone.png" type="image/x-icon">
    <title>Editar Perfil - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
            margin: 0;
            overflow-x: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #fff;
            border-right: 1px solid #dee2e6;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: 0.5s;
        }
        .content {
            margin-left: 250px;
            padding: 40px;
            width: 100%;
            transition: 0.5s;
        }
        nav.mobile-header {
            display: none;
        }
        @media (max-width: 991px) {
            nav.mobile-header {
                display: flex;
            }
            .sidebar {
                transform: translateX(-100%);
            }
            .content {
                margin-left: 0;
                margin-top: 70px;
                padding: 20px;
            }
        }
        .form-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .form-section h4 {
            color: #0468BF;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #F28705;
        }
    </style>
</head>
<body>
    <nav class="mobile-header" style="justify-content: space-between; align-items: center; background: #fff; padding: 0 20px; height: 70px; width: 100%; position: fixed; top: 0; z-index: 1100; border-bottom: 1px solid #ddd;">
        <a href="index.php">
            <img src="../../assets/images/logo.png" alt="Logo" style="height: 40px;">
        </a>
        <div style="display: flex; align-items: center; gap: 15px;">
            <span style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($usuario['nome']); ?></span>
        </div>
    </nav>

    <aside class="sidebar">
        <div class="text-center py-4 d-none d-lg-block">
            <img src="../../assets/images/logo.png" alt="Logo" style="width: 150px;">
        </div>
        
        <ul class="nav-list" style="list-style: none; padding: 0; margin: 0; flex: 1;">
            <li><a href="painel-usuario.php" style="display: flex; align-items: center; color: #333; text-decoration: none; padding: 15px 25px;"><i class="bi bi-arrow-left"></i> Voltar ao Painel</a></li>
        </ul>
    </aside>

    <main class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col">
                    <h2 class="fw-bold" style="color: #0468BF;">
                        <i class="bi bi-person-circle"></i> Editar Perfil
                    </h2>
                    <hr style="width: 50px; border: 2px solid #F28705; opacity: 1;">
                </div>
            </div>

            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($mensagem_sucesso); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($erro_mensagem)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($erro_mensagem); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="acao_atualizar" value="1">

                <!-- Informações Pessoais -->
                <div class="form-section">
                    <h4><i class="bi bi-person-badge"></i> Informações Pessoais</h4>
                    
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="nome" name="nome" required
                                   value="<?php echo htmlspecialchars($dados_usuario['nome'] ?? ''); ?>">
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="email" class="form-label">Email (não alterável)</label>
                            <input type="email" class="form-control" id="email" disabled
                                   value="<?php echo htmlspecialchars($dados_usuario['email']); ?>">
                            <small class="text-muted">Entre em contato com o administrador para alterar email</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control" id="cpf" name="cpf"
                                   value="<?php echo htmlspecialchars($dados_usuario['cpf'] ?? ''); ?>"
                                   placeholder="000.000.000-00">
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label for="rg" class="form-label">RG</label>
                            <input type="text" class="form-control" id="rg" name="rg"
                                   value="<?php echo htmlspecialchars($dados_usuario['rg'] ?? ''); ?>"
                                   placeholder="00.000.000-0">
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                                   value="<?php echo $dados_usuario['data_nascimento'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="celular" class="form-label">Celular</label>
                            <input type="tel" class="form-control" id="celular" name="celular"
                                   value="<?php echo htmlspecialchars($dados_usuario['celular'] ?? ''); ?>"
                                   placeholder="(81) 99999-9999">
                        </div>
                    </div>
                </div>

                <!-- Alterar Senha -->
                <div class="form-section">
                    <h4><i class="bi bi-lock"></i> Alterar Senha</h4>
                    <p class="text-muted">Deixe em branco se não deseja alterar a senha</p>
                    
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label for="senha_atual" class="form-label">Senha Atual</label>
                            <input type="password" class="form-control" id="senha_atual" name="senha_atual">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="nova_senha" name="nova_senha">
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label for="confirma_senha" class="form-label">Confirmar Senha</label>
                            <input type="password" class="form-control" id="confirma_senha" name="confirma_senha">
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" style="background-color: #0468BF; border-color: #0468BF;">
                            <i class="bi bi-check"></i> Salvar Alterações
                        </button>
                        <a href="painel-controle.php" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
