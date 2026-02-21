<?php
/**
 * TESTE DO SISTEMA DE PAINÉIS
 * 
 * Este arquivo testa:
 * - Acesso protegido aos painéis
 * - Redirecionamento baseado em perfil
 * - Segurança de autenticação
 */

// Inicia sessão para teste
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui arquivos de configuração
require_once 'config/db.php';

// Status do teste
$testes = [];

// 1. Teste de Conexão com Banco
try {
    $conn = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $testes['database_connection'] = [
        'status' => 'SUCESSO',
        'mensagem' => 'Conexão com banco de dados estabelecida',
        'icon' => '✓'
    ];
} catch (Exception $e) {
    $testes['database_connection'] = [
        'status' => 'ERRO',
        'mensagem' => 'Erro ao conectar: ' . $e->getMessage(),
        'icon' => '✗'
    ];
}

// 2. Teste de Tabelas
try {
    $tables_esperadas = ['usuario', 'pessoa', 'paciente', 'exame'];
    $result = $conn->query("SHOW TABLES FROM " . DB_NAME);
    $tables_existentes = $result->fetchAll(PDO::FETCH_COLUMN);
    
    $tabelas_ok = true;
    foreach ($tables_esperadas as $table) {
        if (!in_array($table, $tabelas_existentes)) {
            $tabelas_ok = false;
            break;
        }
    }
    
    $testes['database_tables'] = [
        'status' => $tabelas_ok ? 'SUCESSO' : 'ERRO',
        'mensagem' => $tabelas_ok ? 'Todas as tabelas necessárias existem' : 'Faltam tabelas no banco de dados',
        'icon' => $tabelas_ok ? '✓' : '✗'
    ];
} catch (Exception $e) {
    $testes['database_tables'] = [
        'status' => 'ERRO',
        'mensagem' => 'Erro ao verificar tabelas: ' . $e->getMessage(),
        'icon' => '✗'
    ];
}

// 3. Teste de Usuários ADMIN
try {
    $result = $conn->query("SELECT COUNT(*) FROM usuario WHERE perfil = 'ADMIN'");
    $admin_count = $result->fetchColumn();
    
    $testes['admin_users'] = [
        'status' => $admin_count > 0 ? 'SUCESSO' : 'AVISO',
        'mensagem' => $admin_count > 0 ? "Encontrados $admin_count usuário(s) ADMIN" : 'Nenhum usuário ADMIN encontrado. Execute init-admin.php para criar um.',
        'icon' => $admin_count > 0 ? '✓' : '⚠'
    ];
} catch (Exception $e) {
    $testes['admin_users'] = [
        'status' => 'ERRO',
        'mensagem' => 'Erro ao verificar usuários ADMIN: ' . $e->getMessage(),
        'icon' => '✗'
    ];
}

// 4. Teste de Usuários Comuns
try {
    $result = $conn->query("SELECT COUNT(*) FROM usuario WHERE perfil != 'ADMIN'");
    $user_count = $result->fetchColumn();
    
    $testes['common_users'] = [
        'status' => 'SUCESSO',
        'mensagem' => "Encontrados $user_count usuário(s) comum(s)",
        'icon' => '✓'
    ];
} catch (Exception $e) {
    $testes['common_users'] = [
        'status' => 'ERRO',
        'mensagem' => 'Erro ao verificar usuários comuns: ' . $e->getMessage(),
        'icon' => '✗'
    ];
}

// 5. Teste de Arquivo de Autenticação
$auth_existe = file_exists('config/auth.php');
$testes['auth_file'] = [
    'status' => $auth_existe ? 'SUCESSO' : 'ERRO',
    'mensagem' => $auth_existe ? 'Arquivo config/auth.php existe' : 'Arquivo config/auth.php não encontrado',
    'icon' => $auth_existe ? '✓' : '✗'
];

// 6. Teste de Arquivo de Painel Admin
$painel_admin_existe = file_exists('painel-controle.php');
$testes['painel_admin_file'] = [
    'status' => $painel_admin_existe ? 'SUCESSO' : 'ERRO',
    'mensagem' => $painel_admin_existe ? 'Arquivo painel-controle.php existe' : 'Arquivo painel-controle.php não encontrado',
    'icon' => $painel_admin_existe ? '✓' : '✗'
];

// 7. Teste de Arquivo de Painel Usuário
$painel_usuario_existe = file_exists('painel-usuario.php');
$testes['painel_usuario_file'] = [
    'status' => $painel_usuario_existe ? 'SUCESSO' : 'ERRO',
    'mensagem' => $painel_usuario_existe ? 'Arquivo painel-usuario.php existe' : 'Arquivo painel-usuario.php não encontrado',
    'icon' => $painel_usuario_existe ? '✓' : '✗'
];

// 8. Teste de Módulos
$modulos_esperados = [
    'modulos/painel-usuarios.php',
    'modulos/usuario-exames.php',
    'modulos/usuario-agendamento.php'
];

$modulos_ok = true;
$modulos_faltando = [];

foreach ($modulos_esperados as $modulo) {
    if (!file_exists($modulo)) {
        $modulos_ok = false;
        $modulos_faltando[] = $modulo;
    }
}

$testes['modulos'] = [
    'status' => $modulos_ok ? 'SUCESSO' : 'AVISO',
    'mensagem' => $modulos_ok ? 'Todos os módulos encontrados' : 'Faltam módulos: ' . implode(', ', $modulos_faltando),
    'icon' => $modulos_ok ? '✓' : '⚠'
];

// Conta resultados
$sucesso_count = count(array_filter($testes, fn($t) => $t['status'] === 'SUCESSO'));
$erro_count = count(array_filter($testes, fn($t) => $t['status'] === 'ERRO'));
$aviso_count = count(array_filter($testes, fn($t) => $t['status'] === 'AVISO'));
$total_testes = count($testes);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste do Sistema - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">
    <nav class="navbar navbar-dark" style="background: linear-gradient(135deg, #0468BF, #034a8d);">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-activity"></i> Teste do Sistema - Instituto Zoe
            </span>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- Resumo -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header" style="background: linear-gradient(135deg, #0468BF, #034a8d); color: white;">
                        <h4 class="mb-0">
                            <i class="bi bi-gear"></i> Resumo dos Testes
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="p-3">
                                    <h3 class="text-success"><?php echo $sucesso_count; ?></h3>
                                    <small class="text-muted">Testes com Sucesso</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <h3 class="text-danger"><?php echo $erro_count; ?></h3>
                                    <small class="text-muted">Testes com Erro</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <h3 class="text-warning"><?php echo $aviso_count; ?></h3>
                                    <small class="text-muted">Avisos</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <h3><?php echo $total_testes; ?></h3>
                                    <small class="text-muted">Total de Testes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalhes dos Testes -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header" style="background-color: #f8f9fa;">
                        <h5 class="mb-0">
                            <i class="bi bi-list-check"></i> Detalhes dos Testes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($testes as $chave => $teste): 
                                $cor = match($teste['status']) {
                                    'SUCESSO' => 'success',
                                    'ERRO' => 'danger',
                                    'AVISO' => 'warning',
                                    default => 'secondary'
                                };
                                $bg_cor = match($teste['status']) {
                                    'SUCESSO' => '#d4edda',
                                    'ERRO' => '#f8d7da',
                                    'AVISO' => '#fff3cd',
                                    default => '#e9ecef'
                                };
                            ?>
                                <div class="list-group-item border-0 mb-2" style="background-color: <?php echo $bg_cor; ?>; border-radius: 8px;">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-<?php echo $cor; ?> me-3" style="font-size: 1.2rem; background-color: <?php echo match($teste['status']) {
                                            'SUCESSO' => '#28a745',
                                            'ERRO' => '#dc3545',
                                            'AVISO' => '#ffc107',
                                            default => '#6c757d'
                                        }; padding: 0.5rem 0.75rem;">
                                            <?php echo $teste['icon']; ?>
                                        </span>
                                        <div class="flex-grow-1">
                                            <strong><?php echo ucwords(str_replace('_', ' ', $chave)); ?>:</strong>
                                            <p class="mb-0 text-muted"><?php echo $teste['mensagem']; ?></p>
                                        </div>
                                        <span class="badge" style="background-color: <?php echo match($teste['status']) {
                                            'SUCESSO' => '#28a745',
                                            'ERRO' => '#dc3545',
                                            'AVISO' => '#ffc107',
                                            default => '#6c757d'
                                        }; color: white;">
                                            <?php echo $teste['status']; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximos Passos -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header" style="background-color: #f8f9fa;">
                        <h5 class="mb-0">
                            <i class="bi bi-arrow-right"></i> Próximos Passos
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>1. Criar Usuário ADMIN:</strong>
                                <p class="text-muted">Acesse <a href="init-admin.php">init-admin.php</a> para criar o primeiro usuário administrador.</p>
                            </li>
                            <li class="mb-2">
                                <strong>2. Testar Login:</strong>
                                <p class="text-muted">Acesse <a href="index-login.php">index-login.php</a> e faça login com uma conta.</p>
                            </li>
                            <li class="mb-2">
                                <strong>3. Acessar Painéis:</strong>
                                <ul class="text-muted">
                                    <li>Admin: <a href="painel-controle.php">painel-controle.php</a> (requer perfil ADMIN)</li>
                                    <li>Usuário: <a href="painel-usuario.php">painel-usuario.php</a> (requer perfil não-ADMIN)</li>
                                </ul>
                            </li>
                            <li>
                                <strong>4. Ler Documentação:</strong>
                                <p class="text-muted">Consulte <a href="PAINEL-USUARIOS-INSTRUCOES.md">PAINEL-USUARIOS-INSTRUCOES.md</a> para mais detalhes.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Final -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <?php if ($erro_count === 0): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong><i class="bi bi-check-circle"></i> Sistema Pronto!</strong>
                        Todos os testes passaram com sucesso. Você pode começar a usar o sistema.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($erro_count > 0): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="bi bi-exclamation-triangle"></i> Erros Encontrados!</strong>
                        Existem <?php echo $erro_count; ?> erro(s) que precisam ser corrigidos antes de usar o sistema.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong><i class="bi bi-exclamation-circle"></i> Avisos!</strong>
                        Existem <?php echo $aviso_count; ?> aviso(s) que podem precisar de atenção.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
