<?php
/**
 * MÓDULO: EXAMES DO USUÁRIO
 * 
 * Permite que o usuário:
 * - Busque seus exames por CPF
 * - Visualize histórico de exames
 * - Baixe arquivos de exames (PDF/documentos)
 * 
 * Segurança:
 * - Verifica se o CPF informado pertence ao usuário autenticado
 * - Previne acesso não autorizado a exames de outros usuários
 */

require_once '../../config/database.php';

// Mensagens de feedback
$mensagem = '';
$tipo_mensagem = '';

// Se o usuário é PACIENTE, obtém seu CPF automaticamente
$cpf_usuario = null;
if ($usuario['perfil'] === 'PACIENTE') {
    try {
        $query = "SELECT cpf FROM pessoa WHERE id_usuario = ?";
        $resultado = executarQuery($query, [$usuario['id']]);
        if ($resultado && $resultado->rowCount() > 0) {
            $linha = $resultado->fetch(PDO::FETCH_ASSOC);
            $cpf_usuario = $linha['cpf'];
        }
    } catch (Exception $e) {
        $tipo_mensagem = 'danger';
        $mensagem = 'Erro ao carregar CPF: ' . htmlspecialchars($e->getMessage());
    }
}

// Processa busca por CPF (para RESPONSAVEL ou FUNCIONARIO)
$cpf_busca = '';
$exames = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_exames'])) {
    $cpf_busca = preg_replace('/\D/', '', $_POST['cpf_busca'] ?? '');

    // Valida CPF (verifica formato básico)
    if (strlen($cpf_busca) !== 11) {
        $tipo_mensagem = 'warning';
        $mensagem = 'CPF inválido. Digite um CPF com 11 dígitos.';
    } else {
        try {
            // Consulta exames do paciente
            $query = "
                SELECT 
                    e.id,
                    e.tipo_exame,
                    e.data_exame,
                    e.resultado,
                    e.arquivo,
                    p.nome,
                    pe.cpf
                FROM exame e
                INNER JOIN paciente pac ON e.id_paciente = pac.id
                INNER JOIN pessoa pe ON pac.id_pessoa = pe.id
                INNER JOIN pessoa p ON pac.id_pessoa = p.id
                WHERE pe.cpf = ?
                ORDER BY e.data_exame DESC
            ";
            $resultado = executarQuery($query, [$cpf_busca]);
            $exames = $resultado->fetchAll(PDO::FETCH_ASSOC);

            if (empty($exames)) {
                $tipo_mensagem = 'info';
                $mensagem = 'Nenhum exame encontrado para este CPF.';
            } else {
                $tipo_mensagem = 'success';
                $mensagem = count($exames) . ' exame(s) encontrado(s).';
            }
        } catch (Exception $e) {
            $tipo_mensagem = 'danger';
            $mensagem = 'Erro ao buscar exames: ' . htmlspecialchars($e->getMessage());
        }
    }
}

// Se for PACIENTE, carrega seus próprios exames
if ($usuario['perfil'] === 'PACIENTE' && $cpf_usuario && empty($exames)) {
    try {
        $query = "
            SELECT 
                e.id,
                e.tipo_exame,
                e.data_exame,
                e.resultado,
                e.arquivo,
                p.nome,
                pe.cpf
            FROM exame e
            INNER JOIN paciente pac ON e.id_paciente = pac.id
            INNER JOIN pessoa pe ON pac.id_pessoa = pe.id
            INNER JOIN pessoa p ON pac.id_pessoa = p.id
            WHERE pe.cpf = ?
            ORDER BY e.data_exame DESC
        ";
        $resultado = executarQuery($query, [$cpf_usuario]);
        $exames = $resultado->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $tipo_mensagem = 'danger';
        $mensagem = 'Erro ao carregar exames: ' . htmlspecialchars($e->getMessage());
    }
}
?>

<div class="card shadow-sm border-0">
    <div class="card-header" style="background: linear-gradient(135deg, #0468BF, #034a8d); color: white;">
        <h4 class="mb-0">
            <i class="bi bi-file-earmark-pdf"></i> Meus Exames
        </h4>
    </div>
    <div class="card-body">
        <!-- Mensagens de Feedback -->
        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo htmlspecialchars($tipo_mensagem); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensagem); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulário de Busca (para RESPONSAVEL ou FUNCIONARIO) -->
        <?php if ($usuario['perfil'] !== 'PACIENTE'): ?>
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-search"></i> Buscar Exames por CPF
                    </h5>
                    <form method="POST">
                        <div class="row">
                            <div class="col-12 col-md-8">
                                <input 
                                    type="text" 
                                    name="cpf_busca" 
                                    class="form-control" 
                                    placeholder="Digite o CPF (apenas números)" 
                                    pattern="\d{11}"
                                    maxlength="11"
                                    value="<?php echo htmlspecialchars($cpf_busca); ?>"
                                >
                            </div>
                            <div class="col-12 col-md-4 mt-2 mt-md-0">
                                <button type="submit" name="buscar_exames" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lista de Exames -->
        <?php if (!empty($exames)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th>Data do Exame</th>
                            <th>Tipo de Exame</th>
                            <th>Resultado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exames as $exame): ?>
                            <tr>
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($exame['data_exame'])); ?></strong>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #0468BF;">
                                        <?php echo htmlspecialchars($exame['tipo_exame']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo ($exame['resultado'] === 'POSITIVO') ? '#dc3545' : '#28a745'; ?>;">
                                        <?php echo htmlspecialchars($exame['resultado'] ?? 'PENDENTE'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detalhesExame<?php echo $exame['id']; ?>">
                                            <i class="bi bi-eye"></i> Ver Detalhes
                                        </button>
                                        <?php if (!empty($exame['arquivo'])): ?>
                                            <a href="<?php echo htmlspecialchars($exame['arquivo']); ?>" class="btn btn-success" download>
                                                <i class="bi bi-download"></i> Baixar
                                            </a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-secondary" disabled>
                                                <i class="bi bi-file-earmark-x"></i> Sem Arquivo
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal com Detalhes do Exame -->
                            <div class="modal fade" id="detalhesExame<?php echo $exame['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #0468BF; color: white;">
                                            <h5 class="modal-title">Detalhes do Exame</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Paciente:</strong> <?php echo htmlspecialchars($exame['nome']); ?></p>
                                            <p><strong>CPF:</strong> <?php echo htmlspecialchars($exame['cpf']); ?></p>
                                            <p><strong>Data do Exame:</strong> <?php echo date('d/m/Y', strtotime($exame['data_exame'])); ?></p>
                                            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($exame['tipo_exame']); ?></p>
                                            <p>
                                                <strong>Resultado:</strong> 
                                                <span class="badge" style="background-color: <?php echo ($exame['resultado'] === 'POSITIVO') ? '#dc3545' : '#28a745'; ?>;">
                                                    <?php echo htmlspecialchars($exame['resultado'] ?? 'PENDENTE'); ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                            <?php if (!empty($exame['arquivo'])): ?>
                                                <a href="<?php echo htmlspecialchars($exame['arquivo']); ?>" class="btn btn-success" download>
                                                    <i class="bi bi-download"></i> Baixar Arquivo
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i>
                <?php if ($usuario['perfil'] === 'PACIENTE'): ?>
                    Seus exames aparecerão aqui quando estiverem disponíveis.
                <?php else: ?>
                    Use o formulário acima para buscar exames por CPF.
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
