<?php
/**
 * Módulo de Gerenciamento de Usuários
 * Apenas para ADMIN
 */

require_once '../../config/database.php';

// Verificar permissão de ADMIN (já verificado no painel-controle.php, mas reforçamos aqui)
if ($_SESSION['usuario_perfil'] !== 'ADMIN') {
    header('Location: index.php');
    exit;
}

$mensagem_sucesso = '';
$erro_mensagem = '';

// Processar ações de gerenciamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    // Desativar/Ativar usuário
    if ($acao === 'alternar_status') {
        $id_usuario = intval($_POST['id_usuario'] ?? 0);
        
        if ($id_usuario > 0) {
            $sql_check = "SELECT ativo FROM usuario WHERE id_usuario = ?";
            $usuario_atual = obterUmaLinha($sql_check, [$id_usuario]);
            
            if ($usuario_atual) {
                $novo_status = $usuario_atual['ativo'] == 1 ? 0 : 1;
                $sql_update = "UPDATE usuario SET ativo = ? WHERE id_usuario = ?";
                
                if (executarQuery($sql_update, [$novo_status, $id_usuario])) {
                    $acao_txt = $novo_status == 1 ? 'ativado' : 'desativado';
                    $mensagem_sucesso = "Usuário {$acao_txt} com sucesso.";
                } else {
                    $erro_mensagem = 'Erro ao atualizar o status do usuário.';
                }
            }
        }
    }
    
    // Alterar perfil de usuário
    if ($acao === 'alterar_perfil') {
        $id_usuario = intval($_POST['id_usuario'] ?? 0);
        $novo_perfil = $_POST['novo_perfil'] ?? '';
        
        $perfis_validos = array('ADMIN', 'FUNCIONARIO', 'RESPONSAVEL', 'PACIENTE');
        
        if ($id_usuario > 0 && in_array($novo_perfil, $perfis_validos)) {
            $sql_update = "UPDATE usuario SET perfil = ? WHERE id_usuario = ?";
            
            if (executarQuery($sql_update, [$novo_perfil, $id_usuario])) {
                $mensagem_sucesso = "Perfil do usuário alterado com sucesso.";
            } else {
                $erro_mensagem = 'Erro ao alterar o perfil do usuário.';
            }
        }
    }
}

// Obter lista de usuários
$sql_usuarios = "SELECT u.id_usuario, u.email, u.perfil, u.ativo, u.data_cadastro, 
                        p.id_pessoa, p.nome 
                 FROM usuario u
                 LEFT JOIN pessoa p ON u.id_usuario = p.id_usuario
                 ORDER BY u.data_cadastro DESC";

$usuarios = obterTodas($sql_usuarios);
?>

<div class="row">
    <div class="col-12">
        <?php if (!empty($mensagem_sucesso)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($mensagem_sucesso); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($erro_mensagem)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($erro_mensagem); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="table-responsive mt-4">
    <table class="table table-hover table-sm">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Status</th>
                <th>Data Cadastro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Nenhum usuário encontrado
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $user): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($user['nome'] ?? 'Sem nome'); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge <?php 
                                $cor = '';
                                switch($user['perfil']) {
                                    case 'ADMIN': $cor = 'bg-danger'; break;
                                    case 'FUNCIONARIO': $cor = 'bg-warning text-dark'; break;
                                    case 'RESPONSAVEL': $cor = 'bg-info'; break;
                                    case 'PACIENTE': $cor = 'bg-success'; break;
                                }
                                echo $cor;
                            ?>">
                                <?php echo $user['perfil']; ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="acao" value="alternar_status">
                                <input type="hidden" name="id_usuario" value="<?php echo $user['id_usuario']; ?>">
                                <button type="submit" class="btn btn-sm <?php echo $user['ativo'] == 1 ? 'btn-success' : 'btn-danger'; ?>">
                                    <?php echo $user['ativo'] == 1 ? '✓ Ativo' : '✗ Inativo'; ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <small><?php echo date('d/m/Y', strtotime($user['data_cadastro'])); ?></small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil<?php echo $user['id_usuario']; ?>">
                                    <i class="bi bi-pencil"></i> Perfil
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal para alterar perfil -->
                    <div class="modal fade" id="modalEditarPerfil<?php echo $user['id_usuario']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Alterar Perfil</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="">
                                    <div class="modal-body">
                                        <input type="hidden" name="acao" value="alterar_perfil">
                                        <input type="hidden" name="id_usuario" value="<?php echo $user['id_usuario']; ?>">
                                        
                                        <p><strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
                                        
                                        <div class="mb-3">
                                            <label for="novo_perfil<?php echo $user['id_usuario']; ?>" class="form-label">Novo Perfil:</label>
                                            <select id="novo_perfil<?php echo $user['id_usuario']; ?>" name="novo_perfil" class="form-select" required>
                                                <option value="">Selecione...</option>
                                                <option value="ADMIN" <?php echo $user['perfil'] === 'ADMIN' ? 'selected' : ''; ?>>ADMIN</option>
                                                <option value="FUNCIONARIO" <?php echo $user['perfil'] === 'FUNCIONARIO' ? 'selected' : ''; ?>>FUNCIONÁRIO</option>
                                                <option value="RESPONSAVEL" <?php echo $user['perfil'] === 'RESPONSAVEL' ? 'selected' : ''; ?>>RESPONSÁVEL</option>
                                                <option value="PACIENTE" <?php echo $user['perfil'] === 'PACIENTE' ? 'selected' : ''; ?>>PACIENTE</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Salvar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
