<?php
/**
 * PAINEL DO USUÁRIO - ACESSO PARA USUÁRIOS COMUNS
 * 
 * Este painel é restrito a usuários autenticados que NÃO são ADMIN.
 * Os usuários podem:
 * - Visualizar seus dados pessoais
 * - Consultar e baixar exames
 * - Agendar consultas
 * - Editar seu perfil
 * 
 * Funcionalidades:
 * - Painel de Receber Exames: Acesso via CPF (para pacientes)
 * - Agendamento: Disponível para todos os usuários autenticados
 * - Perfil: Edição de dados pessoais e senha
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

// PROTEÇÃO: Apenas não-admin podem acessar este painel
// Se for ADMIN, redireciona para o painel de controle
if ($usuario['perfil'] === 'ADMIN') {
    header('Location: painel-controle.php');
    exit;
}

// Processa logout
if (isset($_GET['logout'])) {
    destruirSessao();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #0468BF, #034a8d);">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-heart-pulse"></i> Instituto Zoe
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($usuario['nome']); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="editar-perfil.php">
                            <i class="bi bi-gear"></i> Configurações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="?logout=1">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container-fluid px-4 py-5">
        <div class="row">
            <!-- Sidebar de Navegação -->
            <div class="col-12 col-md-3 mb-4">
                <div class="list-group shadow-sm">
                    <a href="?aba=principal" class="list-group-item list-group-item-action <?php echo ($aba_ativa === 'principal') ? 'active' : ''; ?>" style="border-left: 4px solid #0468BF;">
                        <i class="bi bi-speedometer2"></i> Painel Principal
                    </a>
                    <a href="?aba=exames" class="list-group-item list-group-item-action <?php echo ($aba_ativa === 'exames') ? 'active' : ''; ?>" style="border-left: 4px solid #0468BF;">
                        <i class="bi bi-file-earmark-pdf"></i> Meus Exames
                    </a>
                    <a href="?aba=agendamento" class="list-group-item list-group-item-action <?php echo ($aba_ativa === 'agendamento') ? 'active' : ''; ?>" style="border-left: 4px solid #0468BF;">
                        <i class="bi bi-calendar2-event"></i> Agendamentos
                    </a>
                    <a href="?aba=perfil" class="list-group-item list-group-item-action <?php echo ($aba_ativa === 'perfil') ? 'active' : ''; ?>" style="border-left: 4px solid #0468BF;">
                        <i class="bi bi-person"></i> Meus Dados
                    </a>
                </div>
            </div>

            <!-- Conteúdo Dinâmico -->
            <div class="col-12 col-md-9">
                <!-- Painel Principal -->
                <?php if ($aba_ativa === 'principal'): ?>
                    <div class="card shadow-sm border-0">
                        <div class="card-header" style="background: linear-gradient(135deg, #0468BF, #034a8d); color: white;">
                            <h4 class="mb-0">
                                <i class="bi bi-speedometer2"></i> Bem-vindo ao seu Painel
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle"></i>
                                <strong>Olá, <?php echo htmlspecialchars(explode(' ', $usuario['nome'])[0]); ?>!</strong>
                                Aqui você pode gerenciar seus exames, agendar consultas e atualizar seus dados.
                            </div>

                            <!-- Cards de Informações -->
                            <div class="row mt-4">
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card border-0" style="background-color: #e3f2fd;">
                                        <div class="card-body text-center">
                                            <i class="bi bi-file-earmark-pdf" style="font-size: 2rem; color: #0468BF;"></i>
                                            <h5 class="card-title mt-3">Meus Exames</h5>
                                            <p class="card-text text-muted small">
                                                Visualize e baixe seus exames
                                            </p>
                                            <a href="?aba=exames" class="btn btn-sm btn-primary">Acessar</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card border-0" style="background-color: #f3e5f5;">
                                        <div class="card-body text-center">
                                            <i class="bi bi-calendar2-event" style="font-size: 2rem; color: #8e24aa;"></i>
                                            <h5 class="card-title mt-3">Agendamentos</h5>
                                            <p class="card-text text-muted small">
                                                Agende suas consultas
                                            </p>
                                            <a href="?aba=agendamento" class="btn btn-sm btn-primary">Agendar</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 mb-3">
                                    <div class="card border-0" style="background-color: #e8f5e9;">
                                        <div class="card-body text-center">
                                            <i class="bi bi-person" style="font-size: 2rem; color: #388e3c;"></i>
                                            <h5 class="card-title mt-3">Meus Dados</h5>
                                            <p class="card-text text-muted small">
                                                Atualize seus informações
                                            </p>
                                            <a href="?aba=perfil" class="btn btn-sm btn-primary">Editar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informações do Usuário -->
                            <div class="mt-5 p-4" style="background-color: #f8f9fa; border-radius: 8px;">
                                <h5 class="mb-3">
                                    <i class="bi bi-info-circle"></i> Informações da Conta
                                </h5>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-2">
                                        <strong>Email:</strong> 
                                        <span class="text-muted"><?php echo htmlspecialchars($usuario['email']); ?></span>
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <strong>Perfil:</strong> 
                                        <span class="badge" style="background-color: #0468BF;">
                                            <?php echo htmlspecialchars($usuario['perfil']); ?>
                                        </span>
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <strong>Status:</strong> 
                                        <span class="badge" style="background-color: <?php echo (isset($usuario['ativo']) && $usuario['ativo'] == 1) ? '#28a745' : '#dc3545'; ?>;">
                                            <?php echo (isset($usuario['ativo']) && $usuario['ativo'] == 1) ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <strong>Membro desde:</strong> 
                                        <span class="text-muted"><?php echo isset($usuario['data_criacao']) ? date('d/m/Y', strtotime($usuario['data_criacao'])) : 'N/A'; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Aba Exames -->
                <?php if ($aba_ativa === 'exames'): ?>
                    <?php require_once 'modulos/usuario-exames.php'; ?>
                <?php endif; ?>

                <!-- Aba Agendamento -->
                <?php if ($aba_ativa === 'agendamento'): ?>
                    <?php require_once 'modulos/usuario-agendamento.php'; ?>
                <?php endif; ?>

                <!-- Aba Perfil -->
                <?php if ($aba_ativa === 'perfil'): ?>
                    <div class="card shadow-sm border-0">
                        <div class="card-header" style="background: linear-gradient(135deg, #0468BF, #034a8d); color: white;">
                            <h4 class="mb-0">
                                <i class="bi bi-person"></i> Meus Dados
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle"></i>
                                Para editar seus dados pessoais, clique no botão abaixo.
                            </div>
                            <a href="editar-perfil.php" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Editar Perfil
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
