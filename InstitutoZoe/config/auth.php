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
        header('Location: index-login.php?sessao_expirada=1');
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
        header('Location: index-login.php');
        exit;
    }
}

/**
 * Verifica se o usuário tem permissão de ADMIN
 * Se não tiver, redireciona para o painel principal
 */
function verificarAdmin() {
    verificarAutenticacao();
    
    if ($_SESSION['usuario_perfil'] !== 'ADMIN') {
        header('Location: painel-controle.php');
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
            'data_criacao' => $_SESSION['usuario_data_criacao'] ?? date('Y-m-d H:i:s')
        );
    }
    return null;
}

/**
 * Cria a sessão do usuário após login bem-sucedido
 * 
 * @param array $usuario Dados do usuário do banco
 * @param array $pessoa Dados da pessoa do banco
 */
function criarSessao($usuario, $pessoa) {
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_perfil'] = $usuario['perfil'];
    $_SESSION['pessoa_id'] = $pessoa['id_pessoa'] ?? null;
    $_SESSION['usuario_nome'] = $pessoa['nome'] ?? '';
    $_SESSION['usuario_ativo'] = $usuario['ativo'] ?? 1;
    $_SESSION['usuario_data_criacao'] = $usuario['data_criacao'] ?? date('Y-m-d H:i:s');
    $_SESSION['email_verificado'] = $usuario['email_verificado'] ?? 0;
    $_SESSION['ultimo_ativo'] = time(); // Iniciar contador de timeout
}
    $_SESSION['pessoa_id'] = $pessoa['id_pessoa'] ?? null;
    $_SESSION['usuario_nome'] = $pessoa['nome'] ?? '';
    $_SESSION['usuario_ativo'] = $usuario['ativo'] ?? 1;
    $_SESSION['usuario_data_criacao'] = $usuario['data_criacao'] ?? date('Y-m-d H:i:s');
}

/**
 * Destrói a sessão do usuário (logout)
 */
function destruirSessao() {
    session_destroy();
    header('Location: index.php');
    exit;
}

?>
