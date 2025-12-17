<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
    body { 
        display: flex; 
        min-height: 100vh; 
        background-color: #f8f9fa; 
        margin: 0; 
        overflow-x: hidden; 
    }

    
    .sidebar {
        width: 200px; 
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

    
    nav {
        display: none; 
        justify-content: space-around; 
        align-items: center;
        background: #fff; 
        height: 8vh; 
        width: 100%;
        position: fixed; 
        top: 0; 
        z-index: 1100; 
        border-bottom: 1px solid #ddd;
    }

    .social-icons a { margin: 0 10px; color: #004ba8; text-decoration: none; }
    .mobile-menu { cursor: pointer; display: none; }
    .mobile-menu div { width: 32px; height: 2px; background: #004ba8; margin: 8px; transition: 0.3s; }

    
    .nav-list { 
        list-style: none; 
        padding: 0; 
        width: 100%; 
        display: flex;
        flex-direction: column;
        height: 100%;
        margin: 0;
    }

    .nav-list li { opacity: 1; padding: 0; } 

    .nav-list li a { 
        display: flex; 
        align-items: center; 
        color: #333; 
        text-decoration: none; 
        font-size: 18px; 
        padding: 15px 25px; 
        transition: 0.3s; 
    }

    .nav-list li a i { 
        margin-right: 15px; 
        color: #0d6efd; 
        font-size: 1.2rem; 
        transition: 0.3s;
    }

    .nav-list li a:hover {
        background-color: #f0f4f8; 
        color: #0d6efd; 
    }

    .nav-list li a:hover i {
        color: #0d6efd;
    }

    .sair-item, #sair {
        margin-top: auto; 
        border-top: 1px solid #dee2e6;
    }

    .sair-item a:hover, #sair a:hover {
        background-color: #fff1f0; 
        color: #dc3545 !important;
    }

    .sair-item a:hover i, #sair a:hover i {
        color: #dc3545 !important;
    }

    /* Começo da animação mobile*/
    .mobile-menu.active .line1 {
        transform: rotate(-45deg) translate(-8px, 8px);
    }

    .mobile-menu.active .line2 {
        opacity: 0;
    }

    .mobile-menu.active .line3 {
        transform: rotate(45deg) translate(-5px, -7px);
    }

    .mobile-menu div {
        transition: 0.3s;
    }

    .content { 
        margin-left: 250px; 
        padding: 30px; 
        width: 100%; 
        transition: 0.5s; 
    }

    @media (max-width: 991px) {
        nav { display: flex; } 
        .mobile-menu { display: block; }
        
        .sidebar {
            position: fixed; 
            top: 8vh; 
            right: 0; 
            width: 100vw; 
            height: 92vh;
            background: #fff; 
            flex-direction: column; 
            align-items: center;
            transform: translateX(100%); 
            left: auto; 
            border: none;
        }

        .content { margin-left: 0; margin-top: 10vh; }
        .sidebar.active { transform: translateX(0); }
    }
    /*Fim da animação mobile*/
    
</style>
</head>
<body>

    <nav>
        <a class="logo" href="index.php">
            <img src="img/logo.png" alt="Logo" style="height: 5vh;">
        </a>

        <div class="social-icons d-none d-md-flex">
            <a href="https://www.instagram.com/instituicao.zoe/" target="_blank"><i class="bi bi-instagram" style="font-size: 3vh;"></i></a>
            <a href="https://www.tiktok.com/@elesabracam" target="_blank"><i class="bi bi-tiktok" style="font-size: 3vh;"></i></a>
            <a href="https://www.youtube.com/channel/UC7ONgI1ulSOE8iYjwWE3Kww" target="_blank"><i class="bi bi-youtube" style="font-size: 4vh;"></i></a>
            <a href="https://wa.me/5581973410768" target="_blank"><i class="bi bi-whatsapp" style="font-size: 3vh;"></i></a>
        </div>

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
            <li><a href="#"><i class="bi bi-person-plus"></i> Cadastro</a></li>
            <li><a href="#"><i class="bi bi-file-earmark-medical"></i> Exame</a></li>
            <li><a href="#"><i class="bi bi-calendar-event"></i> Agendamento</a></li>
            <li><a href="#"><i class="bi bi-people"></i> Usuários</a></li>
            
           

            <li class="sair-item"><a href="#" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
        </ul>
    </aside>

    <main class="content">
        
    </main>
    

    <script>
        class MobileNavbar {
            constructor(mobileMenu, navList, navLinks, sidebar) {
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

        const mobileNavbar = new MobileNavbar(
            ".mobile-menu",
            ".nav-list",
            ".nav-list li",
            ".sidebar",
        );
        mobileNavbar.init();
    </script>
    
</body>
</html>