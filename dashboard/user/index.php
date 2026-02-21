<?php
/**
 * PAINEL DO USUÁRIO - ACESSO PARA USUÁRIOS COMUNS (VERSÃO MELHORADA)
 * 
 * Este painel foi otimizado para uma melhor experiência visual e usabilidade.
 */

// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica autenticação
require_once '../../config/auth.php';
require_once '../../config/database.php';
verificarAutenticacao();

// Obtém dados do usuário autenticado
$usuario = obterDadosUsuario();

// PROTEÇÃO: Verifica o perfil em tempo real no banco de dados
try {
    $query = "SELECT perfil FROM usuario WHERE id_usuario = ? LIMIT 1";
    $resultado = executarQuery($query, [$usuario['id']]);
    
    if ($resultado && $resultado->rowCount() > 0) {
        $dados_db = $resultado->fetch(PDO::FETCH_ASSOC);
        $perfil_atual = $dados_db['perfil'];
        
        if ($perfil_atual !== $usuario['perfil']) {
            $_SESSION['usuario_perfil'] = $perfil_atual;
            $usuario['perfil'] = $perfil_atual;
        }
    }
} catch (Exception $e) {
    // Fallback silencioso
}


// Aba ativa (padrão: principal)
$aba_ativa = isset($_GET['aba']) ? $_GET['aba'] : 'principal';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Painel - Instituto Zoe</title>
    
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
            transition: var(--transition);
        }

        .nav-user-info:hover {
            background: #e2e8f0;
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

        .menu-item i {
            font-size: 1.2rem;
        }

        .menu-item:hover {
            background: #f8fafc;
            color: var(--primary-light);
            transform: translateX(5px);
        }

        .menu-item.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 75, 168, 0.2);
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
            transition: var(--transition);
        }

        .custom-card:hover {
            box-shadow: var(--card-shadow-hover);
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

        /* Welcome Alert */
        .welcome-banner {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
            border: 1px solid #bae6fd;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .welcome-icon {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--primary-light);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* Quick Actions Grid */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: white;
            border: 1px solid #f1f5f9;
            border-radius: 18px;
            padding: 1.5rem;
            text-align: center;
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .action-card:hover {
            border-color: var(--primary-light);
            background: #f8fbff;
            transform: translateY(-5px);
        }

        .action-icon {
            font-size: 2.5rem;
            color: var(--primary-light);
            margin-bottom: 1rem;
            display: inline-block;
        }

        .action-card h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }

        .action-card p {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 1.2rem;
        }

        /* Info Section */
        .info-section {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1.5rem;
        }

        .info-title {
            font-weight: 600;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1e293b;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .info-item {
            padding: 12px;
            background: white;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .info-value {
            font-weight: 500;
            color: #334155;
        }

        /* Badges */
        .badge-custom {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .badge-profile { background: #e0f2fe; color: #075985; }

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

        @media (max-width: 576px) {
            .welcome-banner {
                flex-direction: column;
                text-align: center;
            }
            .card-body-content {
                padding: 1.25rem;
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
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <div class="nav-user-info">
                            <i class="bi bi-person-circle text-primary"></i>
                            <span class="fw-medium"><?php echo htmlspecialchars($usuario['nome']); ?></span>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary btn-sm rounded-pill px-3" href="profile.php">
                            <i class="bi bi-gear me-1"></i> Configurações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger btn-sm rounded-pill px-3" href="<?= BASE_URL ?>logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-menu">
                <a href="?aba=principal" class="menu-item <?php echo ($aba_ativa === 'principal') ? 'active' : ''; ?>">
                    <i class="bi bi-grid-1x2-fill"></i> Painel Principal
                </a>
                <a href="?aba=exames" class="menu-item <?php echo ($aba_ativa === 'exames') ? 'active' : ''; ?>">
                    <i class="bi bi-file-earmark-medical-fill"></i> Meus Exames
                </a>
                <a href="?aba=agendamento" class="menu-item <?php echo ($aba_ativa === 'agendamento') ? 'active' : ''; ?>">
                    <i class="bi bi-calendar-check-fill"></i> Agendamentos
                </a>
                <a href="?aba=perfil" class="menu-item <?php echo ($aba_ativa === 'perfil') ? 'active' : ''; ?>">
                    <i class="bi bi-person-vcard-fill"></i> Meus Dados
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            
            <!-- Aba Principal -->
            <?php if ($aba_ativa === 'principal'): ?>
                <div class="custom-card animate-fade">
                    <div class="card-header-gradient">
                        <h4><i class="bi bi-speedometer2"></i> Visão Geral do Paciente</h4>
                    </div>
                    <div class="card-body-content">
                        
                        <!-- Welcome Banner -->
                        <div class="welcome-banner">
                            <div class="welcome-icon">
                                <i class="bi bi-stars"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold">Olá, <?php echo htmlspecialchars(explode(' ', $usuario['nome'])[0]); ?>!</h4>
                                <p class="mb-0 text-muted">Bem-vindo de volta ao seu portal de saúde. O que deseja fazer hoje?</p>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="actions-grid">
                            <a href="?aba=exames" class="action-card">
                                <i class="bi bi-file-earmark-pdf action-icon"></i>
                                <h5>Meus Exames</h5>
                                <p>Acesse resultados e históricos de exames realizados.</p>
                                <span class="btn btn-primary btn-sm rounded-pill px-4">Ver Tudo</span>
                            </a>

                            <a href="?aba=agendamento" class="action-card">
                                <i class="bi bi-calendar-plus action-icon"></i>
                                <h5>Agendamentos</h5>
                                <p>Marque novas consultas ou gerencie seus horários.</p>
                                <span class="btn btn-primary btn-sm rounded-pill px-4">Agendar</span>
                            </a>

                            <a href="?aba=perfil" class="action-card">
                                <i class="bi bi-person-gear action-icon"></i>
                                <h5>Meus Dados</h5>
                                <p>Mantenha suas informações de contato sempre atualizadas.</p>
                                <span class="btn btn-primary btn-sm rounded-pill px-4">Editar</span>
                            </a>
                        </div>

                        <!-- Account Info Section -->
                        <div class="info-section">
                            <h5 class="info-title">
                                <i class="bi bi-shield-check text-primary"></i> Informações da Conta
                            </h5>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">E-mail</div>
                                    <div class="info-value text-truncate"><?php echo htmlspecialchars($usuario['email']); ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Perfil de Acesso</div>
                                    <div class="info-value">
                                        <span class="badge-custom badge-profile"><?php echo htmlspecialchars($usuario['perfil']); ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Status da Conta</div>
                                    <div class="info-value">
                                        <?php if (isset($usuario['ativo']) && $usuario['ativo'] == 1): ?>
                                            <span class="badge-custom badge-active"><i class="bi bi-check-circle-fill me-1"></i> Ativo</span>
                                        <?php else: ?>
                                            <span class="badge-custom badge-inactive"><i class="bi bi-x-circle-fill me-1"></i> Inativo</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Membro desde</div>
                                    <div class="info-value">
                                        <?php echo isset($usuario['data_criacao']) ? date('d/m/Y', strtotime($usuario['data_criacao'])) : 'N/A'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Aba Exames -->
            <?php if ($aba_ativa === 'exames'): ?>
                <div class="animate-fade">
                    <?php 
                        // Verifica se o arquivo existe antes de incluir para evitar erros fatais
                        if (file_exists('modulos/usuario-exames.php')) {
                            require_once 'modulos/usuario-exames.php';
                        } else {
                            echo '<div class="custom-card p-5 text-center">
                                    <i class="bi bi-exclamation-triangle text-warning display-4"></i>
                                    <h4 class="mt-3">Módulo de Exames não encontrado</h4>
                                    <p class="text-muted">Por favor, entre em contato com o suporte técnico.</p>
                                  </div>';
                        }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Aba Agendamento -->
            <?php if ($aba_ativa === 'agendamento'): ?>
                <div class="animate-fade">
                    <?php 
                        if (file_exists('modulos/usuario-agendamento.php')) {
                            require_once 'modulos/usuario-agendamento.php';
                        } else {
                            echo '<div class="custom-card p-5 text-center">
                                    <i class="bi bi-calendar-x text-warning display-4"></i>
                                    <h4 class="mt-3">Módulo de Agendamento não encontrado</h4>
                                    <p class="text-muted">Por favor, entre em contato com o suporte técnico.</p>
                                  </div>';
                        }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Aba Perfil -->
            <?php if ($aba_ativa === 'perfil'): ?>
                <div class="custom-card animate-fade">
                    <div class="card-header-gradient">
                        <h4><i class="bi bi-person-vcard"></i> Gerenciar Meus Dados</h4>
                    </div>
                    <div class="card-body-content">
                        <div class="welcome-banner mb-4">
                            <div class="welcome-icon">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold">Atualização de Perfil</h5>
                                <p class="mb-0 text-muted">Mantenha seus dados atualizados para facilitar o contato e agendamentos.</p>
                            </div>
                        </div>
                        
                        <div class="text-center py-4">
                            <p class="mb-4">Para alterar sua senha, e-mail ou dados pessoais, clique no botão abaixo para acessar a página de edição completa.</p>
                            <a href="profile.php" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                                <i class="bi bi-person-gear me-2"></i> Acessar Configurações de Perfil
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

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
