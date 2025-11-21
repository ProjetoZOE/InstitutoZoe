<nav>
    <a class="logo" href="index.php" style="display: flex;align-items: center;"> <img src="img/logo.png"
            alt="Logo Instituto Zoe">
    </a>
    <ul class="nave-list">
        <li>
            <a href="<?php 
                echo (basename($_SERVER['PHP_SELF']) === 'index.php' || basename($_SERVER['PHP_SELF']) === '') 
             ? '#sobre' 
             : 'index.php#sobre'; ?>">
                Sobre
            </a>
        </li>
        <li><a href="index-camp.php">Campanhas</a></li>
        <li>
            <button class="Drop btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                style="font-size: 16px;">
                Serviços
            </button>
            <ul class="dropdown-menu" style="align-items: center;">
                <li><a class="dropdown-item" href="index-ativ.php" style="font-size: 16px;">Atividades</a></li>
                <li><a class="dropdown-item" href="index-saude.php" style="font-size: 16px;">Saúde</a>
                </li>
            </ul>
        </li>
        <li><a href="index_agend.php">Agendamento</a></li>
        <li><a href="index-apoiador.php">Seja Apoiador(a)</a></li>
        <li><a href="#faq">FAQ</a></li>
    </ul>
    <div class="social-icons nave-list">
        <a href="https://www.instagram.com/instituicao.zoe/" target="_blank"><i class="bi bi-instagram"
                style="font-size: 3vh;"></i></a>
        <a href="https://www.tiktok.com/@elesabracam?_t=ZM-8yUYqBd8iqW&_r=1" target="_blank"><i
                class="bi bi-tiktok" style="font-size: 3vh;"></i></a>
        <a href="https://www.youtube.com/channel/UC7ONgI1ulSOE8iYjwWE3Kww" target="_blank"><i
                class="bi bi-youtube" style="font-size: 4vh;"></i></a>
        <a href="https://wa.me/5581973410768" target="_blank"><i class="bi bi-whatsapp"
                style="font-size: 3vh;"></i></a>
    </div>
    <div class="mobile-menu">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
        </div>
</nav>