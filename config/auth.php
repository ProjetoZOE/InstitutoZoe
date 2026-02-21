<?php
/**
 * Middleware de Autenticação
 * Verifica se o usuário está autenticado via $_SESSION
 */

// Inicia a sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========================
// TIMEOUT DE SESSÃO (15 minutos de inatividade)
// ========================
$session_timeout = 15 * 60; // 15 minutos em segundos

if (isset($_SESSION['usuario_id'])) {
    // Verificar se última atividade expirou
    if (isset($_SESSION['ultimo_ativo']) && (time() - $_SESSION['ultimo_ativo']) > $session_timeout) {
        // Sessão expirada - destruir e redirecionar
        session_destroy();
        header('Location: ../auth/login.php?sessao_expirada=1');
        exit;
    }
    // Atualizar último tempo de atividade
    $_SESSION['ultimo_ativo'] = time();
}

/**
 * Verifica se o usuário está autenticado
 * Se não estiver, redireciona para a página de login
 */
function verificarAutenticacao() {
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_email'])) {
        header('Location: ../auth/login.php');
        exit;
    }
}

/**
 * Verifica se o usuário tem permissão de ADMIN
 * Se não tiver, redireciona para o painel apropriado
 */
function verificarAdmin() {
    verificarAutenticacao();
    
    if (!isset($_SESSION['usuario_perfil']) || $_SESSION['usuario_perfil'] !== 'ADMIN') {
        // Redireciona para o painel do usuário se não for admin
        header('Location: ../dashboard/user/index.php');
        exit;
    }
}

/**
 * Verifica se o email do usuário foi verificado
 * Se não foi, redireciona para uma página de aviso
 */
function verificarEmailVerificado() {
    verificarAutenticacao();
    
    if (isset($_SESSION['email_verificado']) && $_SESSION['email_verificado'] == 0) {
        header('Location: ../auth/email-nao-verificado.php');
        exit;
    }
}

/**
 * Obtém os dados do usuário autenticado
 * Retorna array com dados da sessão
 */
function obterDadosUsuario() {
    if (isset($_SESSION['usuario_id'])) {
        return array(
            'id' => $_SESSION['usuario_id'],
            'email' => $_SESSION['usuario_email'],
            'perfil' => $_SESSION['usuario_perfil'],
            'pessoa_id' => $_SESSION['pessoa_id'] ?? null,
            'nome' => $_SESSION['usuario_nome'] ?? 'Usuário',
            'ativo' => $_SESSION['usuario_ativo'] ?? 1,
            'email_verificado' => $_SESSION['email_verificado'] ?? 0,
            'data_criacao' => $_SESSION['usuario_data_criacao'] ?? date('Y-m-d H:i:s'),
            'ultimo_ativo' => $_SESSION['ultimo_ativo'] ?? time()
        );
    }
    return null;
}

/**
 * Cria a sessão do usuário após login bem-sucedido
 * 
 * @param array $usuario Dados do usuário do banco
 * @param array $pessoa Dados da pessoa do banco (opcional)
 */
function criarSessao($usuario, $pessoa = null) {
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_perfil'] = $usuario['perfil'];
    $_SESSION['usuario_ativo'] = $usuario['ativo'] ?? 1;
    $_SESSION['usuario_data_criacao'] = $usuario['data_cadastro'] ?? date('Y-m-d H:i:s');
    $_SESSION['email_verificado'] = $usuario['email_verificado'] ?? 0;
    $_SESSION['ultimo_ativo'] = time(); // Iniciar contador de timeout
    
    // Se houver dados da pessoa, adicionar à sessão
    if ($pessoa) {
        $_SESSION['pessoa_id'] = $pessoa['id_pessoa'] ?? null;
        $_SESSION['usuario_nome'] = $pessoa['nome'] ?? 'Usuário';
    } else {
        $_SESSION['pessoa_id'] = null;
        $_SESSION['usuario_nome'] = 'Usuário';
    }
}

// Garante que a sessão exista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carrega configurações globais
require_once __DIR__ . '/config.php';

/**
 * Destrói completamente a sessão do usuário (logout)
 *
 * @param string|null $redirect_url
 */
function destruirSessao(string $redirect_url = null): void
{
    // Define redirecionamento padrão
    if ($redirect_url === null) {
        $redirect_url = BASE_URL . 'index.php';
    }

    // Limpa variáveis de sessão
    $_SESSION = [];

    // Remove cookie da sessão
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    // Destrói a sessão
    session_destroy();

    // Redireciona
    header('Location: ' . $redirect_url);
    exit;
}



/**
 * Verifica se o usuário tem um perfil específico
 * 
 * @param string|array $perfis Perfil ou array de perfis permitidos
 * @return bool true se o usuário tem um dos perfis
 */
function temPerfil($perfis) {
    if (!isset($_SESSION['usuario_perfil'])) {
        return false;
    }
    
    if (is_array($perfis)) {
        return in_array($_SESSION['usuario_perfil'], $perfis);
    }
    
    return $_SESSION['usuario_perfil'] === $perfis;
}

/**
 * Redireciona usuário para o painel apropriado baseado no perfil
 */
function redirecionarParaPainel() {
    if (!isset($_SESSION['usuario_perfil'])) {
        header('Location: ../auth/login.php');
        exit;
    }
    
    switch ($_SESSION['usuario_perfil']) {
        case 'ADMIN':
            header('Location: ../dashboard/admin/index.php');
            break;
        case 'FUNCIONARIO':
            header('Location: ../dashboard/funcionario/index.php');
            break;
        case 'RESPONSAVEL':
        case 'PACIENTE':
        default:
            header('Location: ../dashboard/user/index.php');
            break;
    }
    exit;
}

/**
 * Atualiza o último tempo de atividade
 * Útil para chamar em páginas que fazem requisições AJAX
 */
function atualizarAtividade() {
    if (isset($_SESSION['usuario_id'])) {
        $_SESSION['ultimo_ativo'] = time();
    }
}
?>