<?php
require_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario_autenticado = isset($_SESSION['usuario_id']);
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav>
    <a class="logo" href="<?php echo BASE_URL; ?>index.php" style="display: flex; align-items: center;">
        <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Logo Instituto Zoe">
    </a>

    <ul class="nave-list" style="list-style: none; padding: 0; margin: 0; display: flex; gap: 20px;">
        <li>
            <a href="<?php echo ($current_page === 'index.php' || $current_page === '') ? '#sobre' : BASE_URL . 'index.php#sobre'; ?>">
                Sobre
            </a>
        </li>
        <li><a href="<?php echo BASE_URL; ?>index-camp.php">Campanhas</a></li>
        <li>
            <button class="Drop btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 16px;">
                Serviços
            </button>
            <ul class="dropdown-menu" style="align-items: center;">
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index-ativ.php" style="font-size: 16px;">Atividades</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index-saude.php" style="font-size: 16px;">Saúde</a></li>
            </ul>
        </li>
        <li><a href="<?php echo BASE_URL; ?>index-apoiador.php">Seja Apoiador(a)</a></li>

        <?php if ($current_page !== 'login.php'): ?>
            <li><a href="#faq">FAQ</a></li>
        <?php endif; ?>

        <?php if ($usuario_autenticado): ?>
            <li>
                <a href="<?php 
                    echo BASE_URL;
                    if (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'ADMIN') {
                        echo 'dashboard/admin/index.php';
                    } else {
                        echo 'dashboard/user/index.php';
                    }
                ?>" style="
                    background: linear-gradient(135deg, #0468BF 0%, #0357a8 100%);
                    color: white;
                    padding: 10px 20px;
                    border-radius: 8px;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    font-weight: 500;
                    text-decoration: none;
                    box-shadow: 0 2px 8px rgba(4, 104, 191, 0.3);
                    transition: all 0.3s ease;
                " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(4, 104, 191, 0.4)'" 
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(4, 104, 191, 0.3)'">
                    <i class="bi bi-speedometer2"></i> Meu Painel
                </a>
            </li>
            <li>
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: transparent; border: none; padding: 0; font-size: 1.5rem; color: #0468BF;">
                        <i class="bi bi-person-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px;">
                        <li>
                            <span class="dropdown-item-text" style="font-size: 12px; color: #666;">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars(substr($_SESSION['usuario_nome'] ?? 'Usuário', 0, 30)); ?>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>dashboard/user/profile.php" style="color: #0468BF;"><i class="bi bi-gear"></i> Configurações</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout.php" style="font-weight: 500;"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                    </ul>
                </div>
            </li>
        <?php elseif ($current_page !== 'login.php'): ?>
            <li>
                <a href="<?php echo BASE_URL; ?>auth/login.php" style="
                    background: linear-gradient(135deg, #0468BF 0%, #0357a8 100%);
                    color: white;
                    padding: 10px 20px;
                    border-radius: 8px;
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    font-weight: 500;
                    box-shadow: 0 2px 8px rgba(4, 104, 191, 0.3);
                    transition: all 0.3s ease;
                " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(4, 104, 191, 0.4)'" 
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(4, 104, 191, 0.3)'">
                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="social-icons nave-list" style="list-style: none; display: flex; gap: 15px; padding: 0; margin: 0;">
        <a href="https://www.instagram.com/instituicao.zoe/" target="_blank"><i class="bi bi-instagram" style="font-size: 3vh;"></i></a>
        <a href="https://www.tiktok.com/@elesabracam?_t=ZM-8yUYqBd8iqW&_r=1" target="_blank"><i class="bi bi-tiktok" style="font-size: 3vh;"></i></a>
        <a href="https://www.youtube.com/channel/UC7ONgI1ulSOE8iYjwWE3Kww" target="_blank"><i class="bi bi-youtube" style="font-size: 4vh;"></i></a>
        <a href="https://wa.me/5581973410768" target="_blank"><i class="bi bi-whatsapp" style="font-size: 3vh;"></i></a>
    </div>

    <div class="mobile-menu">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
</nav>
