<?php
/**
 * PAINEL DE CONTROLE - APENAS PARA ADMINISTRADORES (VERSÃO MELHORADA)
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

// PROTEÇÃO: Verifica se o usuário é ADMIN
if ($usuario['perfil'] !== 'ADMIN') {
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acesso Negado - Instituto Zoe</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            body { background: linear-gradient(135deg, #f8faff 0%, #eef2f7 100%); font-family: 'Inter', sans-serif; height: 100vh; display: flex; align-items: center; }
            .error-card { background: white; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); padding: 3rem; text-align: center; max-width: 500px; margin: auto; }
            .error-icon { font-size: 5rem; color: #ef4444; margin-bottom: 1.5rem; }
            .btn-primary { background: #004ba8; border: none; border-radius: 50px; padding: 12px 30px; font-weight: 600; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="error-card animate-fade">
                <i class="bi bi-shield-lock-fill error-icon"></i>
                <h2 class="fw-bold mb-3" style="color: #1e293b;">Acesso Restrito</h2>
                <p class="text-muted mb-4">Desculpe, esta área é exclusiva para administradores do sistema. Seu perfil atual não possui as permissões necessárias.</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="..\user/index.php" class="btn btn-primary"><i class="bi bi-arrow-left me-2"></i>Meu Painel</a>
                    <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4">Ir para Home</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Aba ativa
$aba_ativa = isset($_GET['aba']) ? $_GET['aba'] : 'painel';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - Instituto Zoe</title>
    
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
            --accent-orange: #F28705;
            --bg-gradient: linear-gradient(135deg, #f8faff 0%, #eef2f7 100%);
            --card-shadow: 0 10px 30px rgba(0, 75, 168, 0.08);
            --sidebar-width: 280px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: var(--bg-gradient);
            font-family: 'Inter', sans-serif;
            color: #334155;
            min-height: 100vh;
            overflow: auto !important;
        }

        /* Navbar */
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

        .admin-badge {
            background: #fee2e2;
            color: #991b1b;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            margin-left: 8px;
        }

        /* Layout */
        .dashboard-container {
            display: flex;
            max-width: 1440px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            gap: 2rem;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            flex-shrink: 0;
        }

        .sidebar-menu {
            background: white;
            border-radius: 24px;
            padding: 1.25rem;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 100px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            margin-bottom: 6px;
            border-radius: 14px;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .menu-item i { font-size: 1.2rem; }

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

        .menu-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 1rem 0;
        }

        /* Main Content */
        .main-content { flex-grow: 1; min-width: 0; }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        /* Admin Cards */
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }

        .admin-card {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
            border: 1px solid #f1f5f9;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
        }

        .admin-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 75, 168, 0.1);
            border-color: var(--primary-light);
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .card-blue .icon-wrapper { background: #e0f2fe; color: #0369a1; }
        .card-cyan .icon-wrapper { background: #ecfeff; color: #0891b2; }
        .card-orange .icon-wrapper { background: #fff7ed; color: #c2410c; }
        .card-red .icon-wrapper { background: #fef2f2; color: #b91c1c; }

        .admin-card h5 { font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
        .admin-card p { font-size: 0.875rem; color: #64748b; margin-bottom: 1.5rem; }

        .btn-action {
            margin-top: auto;
            width: 100%;
            border-radius: 12px;
            font-weight: 600;
            padding: 10px;
        }

        /* Mobile */
        @media (max-width: 992px) {
            .dashboard-container { flex-direction: column; margin: 1rem auto; }
            .sidebar { width: 100%; }
            .sidebar-menu { position: static; display: flex; overflow-x: auto; gap: 10px; padding: 0.75rem; }
            .menu-item { margin-bottom: 0; white-space: nowrap; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade { animation: fadeIn 0.5s ease forwards; }
        
        /* Remoção forçada do backdrop via CSS */
        .modal-backdrop { display: none !important; }
    </style>
</head>
<body style="overflow: auto !important;">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-shield-check-fill me-2"></i>Instituto Zoe
                <span class="admin-badge">Admin</span>
            </a>
            
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 bg-light rounded-pill">
                    <i class="bi bi-person-circle text-primary"></i>
                    <span class="fw-medium small"><?php echo htmlspecialchars($usuario['nome']); ?></span>
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
                <a href="?aba=painel" class="menu-item <?php echo ($aba_ativa === 'painel') ? 'active' : ''; ?>">
                    <i class="bi bi-grid-fill"></i> Painel Geral
                </a>
                <a href="?aba=cadastro" class="menu-item <?php echo ($aba_ativa === 'cadastro') ? 'active' : ''; ?>">
                    <i class="bi bi-person-plus-fill"></i> Cadastro
                </a>
                <a href="?aba=exame" class="menu-item <?php echo ($aba_ativa === 'exame') ? 'active' : ''; ?>">
                    <i class="bi bi-file-earmark-medical-fill"></i> Exames
                </a>
                <a href="?aba=agendamento" class="menu-item <?php echo ($aba_ativa === 'agendamento') ? 'active' : ''; ?>">
                    <i class="bi bi-calendar-event-fill"></i> Agendamento
                </a>
                
                <div class="menu-divider"></div>
                
                <a href="?aba=usuarios" class="menu-item <?php echo ($aba_ativa === 'usuarios') ? 'active' : ''; ?>">
                    <i class="bi bi-people-fill"></i> Usuários
                </a>
                <a href="..\user/profile.php" class="menu-item">
                    <i class="bi bi-person-gear-fill"></i> Meu Perfil
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content animate-fade">
            <div class="page-header">
                <h2 class="page-title">
                    <?php 
                    switch($aba_ativa) {
                        case 'cadastro': echo 'Cadastro de Pacientes'; break;
                        case 'exame': echo 'Gerenciar Exames'; break;
                        case 'agendamento': echo 'Controle de Agendamentos'; break;
                        case 'usuarios': echo 'Gestão de Usuários'; break;
                        default: echo 'Painel Administrativo'; break;
                    }
                    ?>
                </h2>
                <p class="text-muted">Bem-vindo ao centro de controle, <?php echo htmlspecialchars(explode(' ', $usuario['nome'])[0]); ?>.</p>
            </div>

            <?php if ($aba_ativa === 'painel'): ?>
                <div class="admin-grid">
                    <!-- Card Cadastro -->
                    <a href="?aba=cadastro" class="admin-card card-blue">
                        <div class="icon-wrapper">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h5>Cadastro</h5>
                        <p>Gerencie pacientes, parceiros e novos registros no sistema.</p>
                        <span class="btn btn-primary btn-action">Acessar Módulo</span>
                    </a>

                    <!-- Card Exames -->
                    <a href="?aba=exame" class="admin-card card-cyan">
                        <div class="icon-wrapper">
                            <i class="bi bi-file-earmark-medical"></i>
                        </div>
                        <h5>Exames</h5>
                        <p>Publique laudos, resultados e gerencie o histórico médico.</p>
                        <span class="btn btn-info btn-action text-white" style="background-color: #0891b2; border: none;">Ver Exames</span>
                    </a>

                    <!-- Card Agendamento -->
                    <a href="?aba=agendamento" class="admin-card card-orange">
                        <div class="icon-wrapper">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h5>Agendamento</h5>
                        <p>Controle a agenda de consultas e horários disponíveis.</p>
                        <span class="btn btn-warning btn-action text-white" style="background-color: #c2410c; border: none;">Gerenciar Agenda</span>
                    </a>

                    <!-- Card Usuários -->
                    <a href="?aba=usuarios" class="admin-card card-red">
                        <div class="icon-wrapper">
                            <i class="bi bi-people"></i>
                        </div>
                        <h5>Usuários</h5>
                        <p>Controle de permissões, perfis e acesso da equipe.</p>
                        <span class="btn btn-danger btn-action" style="background-color: #b91c1c; border: none;">Gestão de Equipe</span>
                    </a>
                </div>

            <?php elseif ($aba_ativa === 'usuarios'): ?>
                <div class="bg-white rounded-4 p-4 shadow-sm">
                    <?php 
                    if (file_exists('../../dashboard/admin/users.php')) {
                        require_once '../../dashboard/admin/users.php';
                    } else {
                        echo '<div class="text-center py-5"><i class="bi bi-search fs-1 text-muted"></i><p class="mt-3">Módulo de usuários não localizado.</p></div>';
                    }
                    ?>
                </div>

            <?php else: ?>
                <!-- Placeholder para módulos em desenvolvimento -->
                <div class="bg-white rounded-4 p-5 text-center shadow-sm">
                    <div class="mb-4">
                        <i class="bi bi-tools display-1 text-light"></i>
                    </div>
                    <h4 class="fw-bold">Módulo em Desenvolvimento</h4>
                    <p class="text-muted mx-auto" style="max-width: 400px;">
                        Estamos trabalhando para trazer as melhores ferramentas de gestão para você. Em breve este módulo estará disponível.
                    </p>
                    <a href="?aba=painel" class="btn btn-outline-primary rounded-pill px-4 mt-3">Voltar ao Início</a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer class="text-center py-4 text-muted mt-auto">
        <small>&copy; <?php echo date('Y'); ?> Instituto Zoe - Painel Administrativo.</small>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());</script>
</body>
</html>
