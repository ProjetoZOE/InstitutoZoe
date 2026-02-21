<?php
/**
 * Edição de Perfil do Usuário Autenticado (VERSÃO MELHORADA)
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
    <title>Editar Perfil - Instituto Zoe</title>
    
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
            --primary-dark: #003375;
            --accent-color: #00d2ff;
            --bg-gradient: linear-gradient(135deg, #f8faff 0%, #eef2f7 100%);
            --card-shadow: 0 10px 30px rgba(0, 75, 168, 0.08);
            --card-shadow-hover: 0 15px 35px rgba(0, 75, 168, 0.12);
            --sidebar-width: 280px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg-gradient);
            font-family: 'Inter', sans-serif;
            color: #334155;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Navbar Customization */
        .navbar {
            background: white !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.04);
            padding: 0.75rem 0;
            z-index: 1030;
        }

        .navbar-brand {
            color: var(--primary-color) !important;
            font-size: 1.4rem;
            letter-spacing: -0.5px;
        }

        .nav-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 16px;
            background: #f1f5f9;
            border-radius: 50px;
        }

        /* Layout Structure */
        .dashboard-container {
            display: flex;
            max-width: 1440px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            gap: 2rem;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            flex-shrink: 0;
        }

        .sidebar-menu {
            background: white;
            border-radius: 20px;
            padding: 1rem;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 100px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            margin-bottom: 8px;
            border-radius: 12px;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .menu-item:hover {
            background: #f8fafc;
            color: var(--primary-light);
            transform: translateX(5px);
        }

        /* Main Content Area */
        .main-content {
            flex-grow: 1;
            min-width: 0;
        }

        /* Card Styling */
        .custom-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header-gradient {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            padding: 1.5rem 2rem;
            color: white;
        }

        .card-header-gradient h4 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-body-content {
            padding: 2rem;
        }

        /* Form Controls */
        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(4, 104, 191, 0.1);
        }

        .form-control:disabled {
            background-color: #f8fafc;
            color: #94a3b8;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 992px) {
            .dashboard-container {
                flex-direction: column;
                margin: 1rem auto;
            }
            .sidebar {
                width: 100%;
            }
            .sidebar-menu {
                position: static;
                display: flex;
                overflow-x: auto;
                gap: 10px;
                padding: 0.75rem;
            }
            .menu-item {
                margin-bottom: 0;
                white-space: nowrap;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-heart-pulse-fill me-2"></i>Instituto Zoe
            </a>
            
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="nav-user-info d-none d-md-flex">
                    <i class="bi bi-person-circle text-primary"></i>
                    <span class="fw-medium"><?php echo htmlspecialchars($usuario['nome']); ?></span>
                </div>
                <a class="btn btn-danger btn-sm rounded-pill px-3" href="<?= BASE_URL ?>logout.php">
                    <i class="bi bi-box-arrow-right me-1"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-menu">
                <a href="index.php" class="menu-item">
                    <i class="bi bi-arrow-left-circle-fill"></i> Voltar ao Painel
                </a>
                <a href="?aba=perfil" class="menu-item active" style="background: var(--primary-color); color: white;">
                    <i class="bi bi-person-vcard-fill"></i> Editar Perfil
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="custom-card animate-fade">
                <div class="card-header-gradient">
                    <h4><i class="bi bi-person-gear"></i> Configurações de Perfil</h4>
                </div>
                
                <div class="card-body-content">
                    <!-- Mensagens de Feedback -->
                    <?php if (!empty($mensagem_sucesso)): ?>
                        <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                            <div><?php echo htmlspecialchars($mensagem_sucesso); ?></div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($erro_mensagem)): ?>
                        <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                            <div><?php echo htmlspecialchars($erro_mensagem); ?></div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="acao_atualizar" value="1">

                        <!-- Seção: Dados Pessoais -->
                        <div class="mb-5">
                            <h5 class="section-title">
                                <i class="bi bi-person-badge text-primary"></i> Informações Pessoais
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="nome" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required
                                           value="<?php echo htmlspecialchars($dados_usuario['nome'] ?? ''); ?>"
                                           placeholder="Seu nome completo">
                                </div>
                                <div class="col-md-4">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="email" disabled
                                           value="<?php echo htmlspecialchars($dados_usuario['email']); ?>">
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">O e-mail não pode ser alterado.</div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="cpf" class="form-label">CPF</label>
                                    <input type="text" class="form-control" id="cpf" name="cpf"
                                           value="<?php echo htmlspecialchars($dados_usuario['cpf'] ?? ''); ?>"
                                           placeholder="000.000.000-00">
                                </div>
                                <div class="col-md-4">
                                    <label for="rg" class="form-label">RG</label>
                                    <input type="text" class="form-control" id="rg" name="rg"
                                           value="<?php echo htmlspecialchars($dados_usuario['rg'] ?? ''); ?>"
                                           placeholder="00.000.000-0">
                                </div>
                                <div class="col-md-4">
                                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                                           value="<?php echo $dados_usuario['data_nascimento'] ?? ''; ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="celular" class="form-label">Celular / WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-whatsapp text-success"></i></span>
                                        <input type="tel" class="form-control border-start-0" id="celular" name="celular"
                                               value="<?php echo htmlspecialchars($dados_usuario['celular'] ?? ''); ?>"
                                               placeholder="(00) 00000-0000">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Segurança -->
                        <div class="mb-5">
                            <h5 class="section-title">
                                <i class="bi bi-shield-lock text-primary"></i> Segurança e Senha
                            </h5>
                            <p class="text-muted small mb-4">Preencha apenas se desejar alterar sua senha de acesso.</p>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="senha_atual" class="form-label">Senha Atual</label>
                                    <input type="password" class="form-control" id="senha_atual" name="senha_atual" placeholder="••••••••">
                                </div>
                                <div class="col-md-4">
                                    <label for="nova_senha" class="form-label">Nova Senha</label>
                                    <input type="password" class="form-control" id="nova_senha" name="nova_senha" placeholder="Mín. 6 caracteres">
                                </div>
                                <div class="col-md-4">
                                    <label for="confirma_senha" class="form-label">Confirmar Nova Senha</label>
                                    <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" placeholder="Repita a nova senha">
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex flex-wrap gap-3 pt-3 border-top">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="bi bi-save2 me-2"></i> Salvar Alterações
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-muted mt-auto">
        <small>&copy; <?php echo date('Y'); ?> Instituto Zoe - Todos os direitos reservados.</small>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
