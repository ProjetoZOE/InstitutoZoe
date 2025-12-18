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
            <img src="img/logo.png" alt="Logo" style="height: 40px;">
        </a>
        <div class="mobile-menu">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
    </nav>

    <aside class="sidebar">
        <div class="text-center py-4 d-none d-lg-block">
            <img src="img/logo.png" alt="Logo" style="width: 150px;">
        </div>
        
        <ul class="nav-list">
            <li><a href="#"><i class="bi bi-sliders"></i> Painel de Controle</a></li>
            <li><a href="#"><i class="bi bi-person-plus"></i> Cadastro</a></li>
            <li><a href="#"><i class="bi bi-file-earmark-medical"></i> Exame</a></li>
            <li><a href="#"><i class="bi bi-calendar-event"></i> Agendamento</a></li>
            <li><a href="#"><i class="bi bi-people"></i> Usuários</a></li>
            <li class="sair-item"><a href="#" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
        </ul>
    </aside>

    <main class="content">    
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col">
                    <h2 class="fw-bold" style="color: #0468BF;">Painel de Controle</h2>
                    <hr style="width: 50px; border: 2px solid #F28705; opacity: 1;">
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 action-card" style="border-top-color: #0468BF;">
                        <div class="card-body">
                            <div class="icon-box-circle bg-zoe-blue">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <h5 class="fw-bold">Cadastro</h5>
                            <p class="text-muted small">Pacientes e Parceiros</p>
                            <a href="#" class="btn rounded-pill px-4 shadow-sm" style="background-color: #0468BF; color: white;">Acessar</a>
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
                            <a href="#" class="btn rounded-pill px-4 shadow-sm" style="background-color: #05AFF2; color: white;">Acessar</a>
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
                            <a href="#" class="btn rounded-pill px-4 shadow-sm" style="background-color: #F28705; color: white;">Acessar</a>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 action-card" style="border-top-color: #F27405;">
                        <div class="card-body">
                            <div class="icon-box-circle bg-zoe-dark-orange">
                                <i class="bi bi-people"></i>
                            </div>
                            <h5 class="fw-bold">Usuários</h5>
                            <p class="text-muted small">Controle de Equipe</p>
                            <a href="#" class="btn rounded-pill px-4 shadow-sm" style="background-color: #F27405; color: white;">Acessar</a>
                        </div>
                    </div>
                </div>

            </div>
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