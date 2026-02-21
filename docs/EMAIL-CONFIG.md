<?php
/**
 * GUIA DE CONFIGURAÇÃO DE EMAIL
 * 
 * Acesso: http://localhost/InstitutoZoe/config-email.php
 * 
 * Este arquivo mostra como configurar email real no seu sistema
 */

// Apenas localhost
$ip_cliente = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($ip_cliente, array('127.0.0.1', 'localhost', '::1'))) {
    http_response_code(403);
    die('❌ Acesso negado - apenas localhost');
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração de Email - Instituto Zoe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 40px 0; }
        .container { max-width: 800px; background: white; padding: 40px; border-radius: 8px; }
        code { background: #f0f0f0; padding: 10px; display: block; margin: 10px 0; border-radius: 4px; }
        .option { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">📧 Configuração de Email</h2>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> <strong>Status Atual:</strong><br>
            Sistema em <strong>MODO DESENVOLVIMENTO</strong> - Emails não são enviados<br>
            Use uma das opções abaixo para configurar email real em produção
        </div>

        <!-- OPÇÃO 1: Gmail SMTP -->
        <div class="option">
            <h4><i class="bi bi-google"></i> Opção 1: Gmail SMTP (Recomendado)</h4>
            <p>Usa servidor SMTP do Gmail para enviar emails</p>
            
            <h6>Passo 1: Ativar Conta de App no Gmail</h6>
            <ol>
                <li>Acesse: <a href="https://myaccount.google.com/apppasswords" target="_blank">https://myaccount.google.com/apppasswords</a></li>
                <li>Selecione "Mail" e "Windows Computer"</li>
                <li>Copie a senha gerada (16 caracteres)</li>
            </ol>

            <h6>Passo 2: Instalar PHPMailer (recomendado)</h6>
            <code>composer require phpmailer/phpmailer</code>

            <h6>Passo 3: Editar config/mailer.php</h6>
            <code>
$mail->Host = 'smtp.gmail.com';<br>
$mail->SMTPAuth = true;<br>
$mail->Username = 'seu-email@gmail.com';<br>
$mail->Password = 'sua-senha-app-16-caracteres';<br>
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;<br>
$mail->Port = 587;
            </code>
        </div>

        <!-- OPÇÃO 2: Hostinger/Hospedagem -->
        <div class="option">
            <h4><i class="bi bi-cloud"></i> Opção 2: SMTP da sua Hospedagem</h4>
            <p>Use credenciais SMTP fornecidas pela sua hospedagem</p>
            
            <h6>Editar config/mailer.php</h6>
            <code>
$mail->Host = 'smtp.seu-servidor.com';<br>
$mail->SMTPAuth = true;<br>
$mail->Username = 'seu-email@seu-dominio.com';<br>
$mail->Password = 'sua-senha-smtp';<br>
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;<br>
$mail->Port = 587; // ou 25, 465 conforme fornecido
            </code>
            
            <div class="alert alert-warning mt-3">
                <strong>💡 Dica:</strong> Verifique email recebido de sua hospedagem com dados de acesso SMTP
            </div>
        </div>

        <!-- OPÇÃO 3: Sendgrid -->
        <div class="option">
            <h4><i class="bi bi-envelope"></i> Opção 3: SendGrid (Gratuito até 100/dia)</h4>
            <p>Serviço especializado em transactionais emails</p>
            
            <h6>Passo 1: Criar conta gratuita</h6>
            <a href="https://sendgrid.com/" target="_blank" class="btn btn-sm btn-primary">Acesse SendGrid</a>

            <h6>Passo 2: Gerar API Key</h6>
            <code>
$mail->Host = 'smtp.sendgrid.net';<br>
$mail->SMTPAuth = true;<br>
$mail->Username = 'apikey';<br>
$mail->Password = 'sua-api-key-sendgrid';<br>
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;<br>
$mail->Port = 587;
            </code>
        </div>

        <!-- OPÇÃO 4: Mailtrap (Desenvolvimento) -->
        <div class="option">
            <h4><i class="bi bi-bug"></i> Opção 4: Mailtrap (Para Testes)</h4>
            <p>Captura emails em ambiente de desenvolvimento</p>
            
            <h6>Passo 1: Criar conta gratuita</h6>
            <a href="https://mailtrap.io/" target="_blank" class="btn btn-sm btn-primary">Acesse Mailtrap</a>

            <h6>Passo 2: Copiar credenciais da aba SMTP</h6>
            <code>
$mail->Host = 'smtp.mailtrap.io';<br>
$mail->SMTPAuth = true;<br>
$mail->Username = 'seu-username';<br>
$mail->Password = 'seu-password';<br>
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;<br>
$mail->Port = 2525;
            </code>

            <div class="alert alert-info mt-3">
                <strong>ℹ️ Benefício:</strong> Todos os emails são capturados em dashboard - perfeito para testes!
            </div>
        </div>

        <!-- Instruções Finais -->
        <div class="alert alert-success mt-4">
            <h5><i class="bi bi-check-circle"></i> Resumo de Configuração</h5>
            <ol>
                <li>Escolha uma opção acima</li>
                <li>Instale PHPMailer: <code>composer require phpmailer/phpmailer</code></li>
                <li>Edite <code>config/mailer.php</code> com suas credenciais</li>
                <li>Teste cadastro de novo usuário</li>
                <li>Verifique se email foi recebido</li>
            </ol>
        </div>

        <!-- Status Atual -->
        <div class="row mt-5">
            <div class="col-md-6">
                <h5>Função mail() nativa</h5>
                <p class="status-ok">✅ Funcionando (modo fallback)</p>
                <small class="text-muted">Sistema continua funcionando mesmo sem SMTP configurado</small>
            </div>
            <div class="col-md-6">
                <h5>Email de Verificação</h5>
                <p class="status-error">⚠️ Não enviando (configure SMTP)</p>
                <small class="text-muted">Use opções acima para ativar</small>
            </div>
        </div>

        <hr class="my-5">

        <h5>❓ Dúvidas Frequentes</h5>
        
        <div class="mb-3">
            <strong>P: E se não configurar email?</strong><br>
            R: Sistema continua funcionando, mas usuários não receberão emails de verificação. Para produção, configure obrigatoriamente.
        </div>

        <div class="mb-3">
            <strong>P: Qual opção é mais segura?</strong><br>
            R: SendGrid ou Mailgun (recomendados para produção). Gmail funciona mas tem limite de taxa.
        </div>

        <div class="mb-3">
            <strong>P: Precisa instalar PHPMailer?</strong><br>
            R: Não obrigatório, sistema usa mail() nativo. Mas PHPMailer é mais robusto e seguro.
        </div>

        <div class="mb-3">
            <strong>P: Como faço em ambiente local?</strong><br>
            R: Use Mailtrap (recomendado) ou Mail Hog (self-hosted).
        </div>

        <a href="index-login.php" class="btn btn-primary mt-4">Voltar ao Login</a>
    </div>
</body>
</html>
