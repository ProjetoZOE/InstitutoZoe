<?php
// Inicia a sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está autenticado
$usuario_autenticado = isset($_SESSION['usuario_id']);
?>

<nav>
    <a class="logo" href="index.php" style="display: flex;align-items: center;"> <img src="assets/images/logo.png"
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
        <li><a href="index-apoiador.php">Seja Apoiador(a)</a></li>
        <li><a href="#faq">FAQ</a></li>
        
        <!-- Botão Entrar/Painel (dinâmico baseado em autenticação) -->
        <?php if ($usuario_autenticado): ?>
            <!-- Usuário logado: Mostrar botão de Painel e Logout -->
            <li>
                <a href="<?php 
                    // Redireciona para o painel apropriado baseado no perfil
                    if (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'ADMIN') {
                        echo 'painel-controle.php';
                    } else {
                        echo 'painel-usuario.php';
                    }
                ?>" style="
                    background-color: #0468BF;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 5px;
                    text-decoration: none;
                    display: inline-block;
                    transition: background-color 0.3s;
                " onmouseover="this.style.backgroundColor='#034a8d'" onmouseout="this.style.backgroundColor='#0468BF'">
                    <i class="bi bi-speedometer2"></i> Meu Painel
                </a>
            </li>
            <li>
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="
                        background-color: transparent;
                        color: #0468BF;
                        border: none;
                        padding: 0;
                        font-size: 1.5rem;
                    ">
                        <i class="bi bi-person-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px;">
                        <li>
                            <span class="dropdown-item-text" style="font-size: 12px; color: #666;">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars(substr($_SESSION['usuario_nome'] ?? 'Usuário', 0, 30)); ?>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="editar-perfil.php" style="color: #0468BF;">
                                <i class="bi bi-gear"></i> Configurações
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="?logout=1" style="font-weight: 500;">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        <?php else: ?>
            <!-- Usuário não logado: Mostrar botão Entrar -->
            <li>
                <a href="index-login.php" style="
                    background-color: #0468BF;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 5px;
                    text-decoration: none;
                    display: inline-block;
                    transition: background-color 0.3s;
                " onmouseover="this.style.backgroundColor='#034a8d'" onmouseout="this.style.backgroundColor='#0468BF'">
                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                </a>
            </li>
        <?php endif; ?>
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