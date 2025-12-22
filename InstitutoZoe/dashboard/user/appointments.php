<?php
/**
 * MÓDULO: AGENDAMENTO DO USUÁRIO
 * 
 * Permite que o usuário:
 * - Visualize seus agendamentos pendentes
 * - Cancele agendamentos
 * - Veja histórico de agendamentos passados
 * 
 * Funcionalidades futuras:
 * - Criar novo agendamento
 * - Receber notificações de confirmação
 */
?>

<div class="card shadow-sm border-0">
    <div class="card-header" style="background: linear-gradient(135deg, #0468BF, #034a8d); color: white;">
        <h4 class="mb-0">
            <i class="bi bi-calendar2-event"></i> Meus Agendamentos
        </h4>
    </div>
    <div class="card-body">
        <!-- Alert de Informação -->
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i>
            <strong>Sistema de Agendamento</strong> - Funcionalidade em desenvolvimento. 
            Por enquanto, entre em contato conosco pelo telefone ou fale-conosco para agendar uma consulta.
        </div>

        <!-- Atalho Rápido -->
        <div class="row mt-4">
            <div class="col-12 col-md-6">
                <div class="card border-0" style="background-color: #e3f2fd;">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-telephone"></i> Entre em Contato
                        </h5>
                        <p class="card-text">
                            Para agendar uma consulta ou esclarecer dúvidas, entre em contato conosco.
                        </p>
                        <a href="fale-conosco.php" class="btn btn-primary">
                            <i class="bi bi-envelope"></i> Fale Conosco
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card border-0" style="background-color: #f3e5f5;">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-telephone"></i> Ligue para Nós
                        </h5>
                        <p class="card-text">
                            Atendimento telefônico de segunda a sexta, das 8h às 17h.
                        </p>
                        <p class="mb-0">
                            <strong style="color: #0468BF;">Telefone: (XX) XXXX-XXXX</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações sobre o Processo -->
        <div class="mt-5 p-4" style="background-color: #f8f9fa; border-radius: 8px;">
            <h5 class="mb-3">
                <i class="bi bi-question-circle"></i> Como Funciona o Agendamento?
            </h5>
            <ol class="mb-0">
                <li class="mb-2">Entre em contato conosco pelos canais disponíveis</li>
                <li class="mb-2">Forneça suas informações e disponibilidade</li>
                <li class="mb-2">Nossa equipe confirmará a data e horário</li>
                <li class="mb-2">Você receberá uma confirmação por email</li>
                <li>Compareça à consulta no horário agendado</li>
            </ol>
        </div>
    </div>
</div>
