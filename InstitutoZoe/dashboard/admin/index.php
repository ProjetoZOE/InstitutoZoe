<?php
/**
 * PAINEL DE CONTROLE - APENAS PARA ADMINISTRADORES
 * 
 * Este arquivo é um painel exclusivo para usuários com perfil ADMIN.
 * Qualquer usuário comum que tentar acessar será redirecionado com uma mensagem amigável.
 * 
 * Funcionalidades:
 * - Gerenciamento de usuários (ativar/desativar/alterar perfis)
 * - Cadastro de pacientes
 * - Gerenciamento de exames
 * - Agendamento
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
// Isso garante que mudanças no perfil sejam refletidas imediatamente
try {
    $query = "SELECT perfil FROM usuario WHERE id_usuario = ? LIMIT 1";
    $resultado = executarQuery($query, [$usuario['id']]);
    
    if ($resultado && $resultado->rowCount() > 0) {
        $dados_db = $resultado->fetch(PDO::FETCH_ASSOC);
        $perfil_atual = $dados_db['perfil'];
        
        // Se o perfil foi alterado, atualiza a sessão
        if ($perfil_atual !== $usuario['perfil']) {
            $_SESSION['usuario_perfil'] = $perfil_atual;
            $usuario['perfil'] = $perfil_atual;
        }
    }
} catch (Exception $e) {
    // Se houver erro na query, usa o perfil da sessão
    // (fallback para manter funcionando)
}

// PROTEÇÃO: Verifica se o usuário é ADMIN
// Se não for, exibe mensagem de acesso negado e oferece redirecionamento seguro
if ($usuario['perfil'] !== 'ADMIN') {
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acesso Negado</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    </head>
    <body style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f8f9fa;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6">
                    <div class="card shadow-lg border-0">
                        <div class="card-body text-center p-5">
                            <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: #dc3545;"></i>
                            <h2 class="card-title mt-3" style="color: #0468BF;">Acesso Negado</h2>
                            <p class="card-text text-muted mt-3">
                                <strong>Apenas administradores podem visualizar esta página.</strong>
                            </p>
                            <p class="card-text text-muted">
                                O painel de controle é restrito a usuários com permissão administrativa. 
                                Se você acredita que deveria ter acesso, entre em contato com o administrador do sistema.
                            </p>
                            <div class="mt-4">
                                <a href="painel-usuario.php" class="btn btn-primary me-2">
                                    <i class="bi bi-arrow-left"></i> Voltar para meu Painel
                                </a>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-house"></i> Ir para Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Processa logout
if (isset($_GET['logout'])) {
    destruirSessao();
}

// Arquivo de conteúdo dinâmico baseado na aba
$aba_ativa = isset($_GET['aba']) ? $_GET['aba'] : 'painel';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - Instituto Zoe</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <style>
        /* 1. Estrutura Base */
        body { 
            display: flex; 
            min-height: 100vh; 
            background-color: #f8f9fa; 
            margin: 0; 
            overflow-x: hidden; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* 2. Sidebar (Desktop) */
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

        .nav-list { 
            list-style: none; 
            padding: 0; 
            width: 100%; 
            display: flex;
            flex-direction: column;
            height: 100%;
            margin: 0;
        }

        .nav-list li a { 
            display: flex; 
            align-items: center; 
            color: #333; 
            text-decoration: none; 
            padding: 15px 25px; 
            transition: 0.3s;
            font-size: 16px; 
        }

        .nav-list li a i { 
            margin-right: 15px; 
            color: #0468BF; 
            font-size: 1.2rem; 
        }

        .nav-list li a:hover {
            background-color: #f0f4f8; 
            color: #0468BF; 
        }

        .sair-item {
            margin-top: auto; 
            border-top: 1px solid #dee2e6;
        }

        .sair-item a:hover {
            background-color: #fff1f0; 
            color: #dc3545 !important;
        }

        .perfil-item {
            border-top: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
        }

        .perfil-item a:hover {
            background-color: #e8f4fd;
            color: #0468BF !important;
        }

        /* 3. Navegação Mobile (Header) */
        nav.mobile-header {
            display: none; 
            justify-content: space-between; 
            align-items: center;
            background: #fff; 
            padding: 0 20px;
            height: 70px; 
            width: 100%;
            position: fixed; 
            top: 0; 
            z-index: 1100; 
            border-bottom: 1px solid #ddd;
        }

        .mobile-menu { cursor: pointer; }
        .mobile-menu div { 
            width: 30px; 
            height: 3px; 
            background: #0468BF; 
            margin: 6px; 
            transition: 0.3s; 
        }

        /* 4. Área de Conteúdo Principal */
        .content { 
            margin-left: 250px; 
            padding: 40px; 
            width: 100%; 
            transition: 0.5s; 
        }

        /* 5. Estilos dos Cards (Paleta de Cores) */
        .bg-zoe-blue { background-color: #0468BF !important; color: white; }
        .bg-zoe-light-blue { background-color: #05AFF2 !important; color: white; }
        .bg-zoe-orange { background-color: #F28705 !important; color: white; }
        .bg-zoe-dark-orange { background-color: #F27405 !important; color: white; }

        .action-card {
            transition: all 0.3s ease;
            border-top: 5px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        .icon-box-circle {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 15px;
            font-size: 1.8rem;
        }

        /* 6. Responsividade (Mobile) */
        @media (max-width: 991px) {
            nav.mobile-header { display: flex; } 
            
            .sidebar {
                top: 70px; 
                right: 0; 
                left: auto;
                width: 100%; 
                height: calc(100vh - 70px);
                transform: translateX(100%); 
                border-right: none;
            }

            .sidebar.active { transform: translateX(0); }
            .content { margin-left: 0; margin-top: 80px; padding: 20px; }

            /* Animação Hamburger */
            .mobile-menu.active .line1 { transform: rotate(-45deg) translate(-8px, 8px); }
            .mobile-menu.active .line2 { opacity: 0; }
            .mobile-menu.active .line3 { transform: rotate(45deg) translate(-5px, -7px); }
        }
    </style>
</head>
<body>

    <nav class="mobile-header">
        <a href="index.php">
            <img src="../../assets/images/logo.png" alt="Logo" style="height: 40px;">
        </a>
        <div style="display: flex; align-items: center; gap: 15px;">
            <span style="font-size: 14px; color: #333;"><?php echo htmlspecialchars($usuario['nome']); ?></span>
            <div class="mobile-menu">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
        </div>
    </nav>

    <aside class="sidebar">
        <div class="text-center py-4 d-none d-lg-block">
            <img src="../../assets/images/logo.png" alt="Logo" style="width: 150px;">
        </div>
        
        <ul class="nav-list">
            <li><a href="painel-controle.php?aba=painel"><i class="bi bi-sliders"></i> Painel de Controle</a></li>
            <li><a href="painel-controle.php?aba=cadastro"><i class="bi bi-person-plus"></i> Cadastro</a></li>
            <li><a href="painel-controle.php?aba=exame"><i class="bi bi-file-earmark-medical"></i> Exame</a></li>
            <li><a href="painel-controle.php?aba=agendamento"><i class="bi bi-calendar-event"></i> Agendamento</a></li>
            <?php if ($usuario['perfil'] === 'ADMIN'): ?>
                <li><a href="painel-controle.php?aba=usuarios"><i class="bi bi-people"></i> Usuários</a></li>
            <?php endif; ?>
            <li class="perfil-item"><a href="editar-perfil.php"><i class="bi bi-person-gear"></i> Meu Perfil</a></li>
            <li class="sair-item"><a href="painel-controle.php?logout=1" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
        </ul>
    </aside>

    <main class="content">    
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col">
                    <h2 class="fw-bold" style="color: #0468BF;">
                        <?php 
                        switch($aba_ativa) {
                            case 'cadastro': echo 'Cadastro de Pacientes'; break;
                            case 'exame': echo 'Exames'; break;
                            case 'agendamento': echo 'Agendamento'; break;
                            case 'usuarios': echo 'Gerenciar Usuários'; break;
                            default: echo 'Painel de Controle'; break;
                        }
                        ?>
                    </h2>
                    <p style="color: #666;">Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>! (<?php echo $usuario['perfil']; ?>)</p>
                    <hr style="width: 50px; border: 2px solid #F28705; opacity: 1;">
                </div>
            </div>

            <?php if ($aba_ativa === 'painel'): ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                    
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm text-center py-4 action-card" style="border-top-color: #0468BF;">
                            <div class="card-body">
                                <div class="icon-box-circle bg-zoe-blue">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                                <h5 class="fw-bold">Cadastro</h5>
                                <p class="text-muted small">Pacientes e Parceiros</p>
                                <a href="painel-controle.php?aba=cadastro" class="btn rounded-pill px-4 shadow-sm" style="background-color: #0468BF; color: white;">Acessar</a>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm text-center py-4 action-card" style="border-top-color: #05AFF2;">
                            <div class="card-body">
                                <div class="icon-box-circle bg-zoe-light-blue">
                                    <i class="bi bi-file-earmark-medical"></i>
                                </div>
                                <h5 class="fw-bold">Exame</h5>
                                <p class="text-muted small">Laudos e Resultados</p>
                                <a href="painel-controle.php?aba=exame" class="btn rounded-pill px-4 shadow-sm" style="background-color: #05AFF2; color: white;">Acessar</a>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm text-center py-4 action-card" style="border-top-color: #F28705;">
                            <div class="card-body">
                                <div class="icon-box-circle bg-zoe-orange">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <h5 class="fw-bold">Agendamento</h5>
                                <p class="text-muted small">Controle de Consultas</p>
                                <a href="painel-controle.php?aba=agendamento" class="btn rounded-pill px-4 shadow-sm" style="background-color: #F28705; color: white;">Acessar</a>
                            </div>
                        </div>
                    </div>

                    <?php if ($usuario['perfil'] === 'ADMIN'): ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm text-center py-4 action-card" style="border-top-color: #F27405;">
                                <div class="card-body">
                                    <div class="icon-box-circle bg-zoe-dark-orange">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <h5 class="fw-bold">Usuários</h5>
                                    <p class="text-muted small">Controle de Equipe</p>
                                    <a href="painel-controle.php?aba=usuarios" class="btn rounded-pill px-4 shadow-sm" style="background-color: #F27405; color: white;">Acessar</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

            <?php elseif ($aba_ativa === 'usuarios'): ?>
                <?php require_once '../../dashboard/admin/users.php'; ?>

            <?php elseif ($aba_ativa === 'cadastro'): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Módulo de cadastro em desenvolvimento
                </div>

            <?php elseif ($aba_ativa === 'exame'): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Módulo de exames em desenvolvimento
                </div>

            <?php elseif ($aba_ativa === 'agendamento'): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Módulo de agendamento em desenvolvimento
                </div>

            <?php endif; ?>
        </div>
    </main>

    <script>
        class MobileNavbar {
            constructor(mobileMenu, sidebar) {
                this.mobileMenu = document.querySelector(mobileMenu);
                this.sidebar = document.querySelector(sidebar);
                this.activeClass = "active";
                this.handleClick = this.handleClick.bind(this);
            }

            handleClick() {
                this.sidebar.classList.toggle(this.activeClass);
                this.mobileMenu.classList.toggle(this.activeClass);
            }

            init() {
                if (this.mobileMenu) {
                    this.mobileMenu.addEventListener("click", this.handleClick);
                }
                return this;
            }
        }

        const mobileNavbar = new MobileNavbar(".mobile-menu", ".sidebar");
        mobileNavbar.init();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>