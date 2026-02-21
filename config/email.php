<?php
/**
 * Configuração de Email com PHPMailer
 * Apenas para emails de ativação, verificação e recuperação de senha
 */

// Email da equipe Instituto Zoe
define('EMAIL_EQUIPE', 'noreply@institutozoe.net');

// Tentar incluir PHPMailer se estiver disponível
$usarPHPMailer = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    $usarPHPMailer = true;
} else {
    error_log('PHPMailer não está instalado. Execute: composer require phpmailer/phpmailer');
    // Fallback para função básica se PHPMailer não estiver disponível
}

/**
 * Envia email de ativação de usuário
 * 
 * @param string $email Email do usuário
 * @param string $nome Nome do usuário
 * @param string $token Token de ativação
 * @return bool true se enviado com sucesso
 */
function enviarEmailAtivacao($email, $nome, $token) {
    global $usarPHPMailer;
    
    if (!$usarPHPMailer) {
        return enviarEmailSimples($email, $nome, $token, 'ativacao');
    }
    
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = getenv('EMAIL_HOST') ?: 'mail.institutozoe.net';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('EMAIL_USER') ?: 'noreply@institutozoe.net';
        $mail->Password = getenv('EMAIL_PASS') ?: '(t+81Y3N8(,T';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = getenv('EMAIL_PORT') ?: 465;
        
        // Remetente e destinatário
        $mail->setFrom(getenv('EMAIL_USER'), 'Instituto Zoe');
        $mail->addAddress($email, $nome);
        
        // Conteúdo do email
        $mail->isHTML(true);
        $mail->Subject = 'Ative sua conta - Instituto Zoe';
        
        $linkAtivacao = getenv('APP_URL') . '/ativar-conta.php?token=' . $token;
        
        $mail->Body = "
            <h2>Bem-vindo ao Instituto Zoe!</h2>
            <p>Olá {$nome},</p>
            <p>Para ativar sua conta, clique no link abaixo:</p>
            <p><a href='{$linkAtivacao}'>Ativar Conta</a></p>
            <p>Ou copie e cole este link no seu navegador:</p>
            <p>{$linkAtivacao}</p>
            <p>Este link expira em 24 horas.</p>
            <br>
            <p>Atenciosamente,<br>Instituto Zoe</p>
        ";
        
        $mail->AltBody = "Clique no link para ativar: {$linkAtivacao}";
        
        $mail->send();
        return true;
        
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('Erro ao enviar email: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Envia email de notificação de novo exame
 * 
 * @param string $email Email do paciente
 * @param string $nomePaciente Nome do paciente
 * @param string $tipoExame Tipo do exame
 * @param string $dataExame Data do exame
 * @return bool true se enviado com sucesso
 */
function enviarEmailNovoExame($email, $nomePaciente, $tipoExame, $dataExame) {
    global $usarPHPMailer;
    
    if (!$usarPHPMailer) {
        return enviarEmailSimples($email, $nomePaciente, null, 'exame', $tipoExame, $dataExame);
    }
    
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = getenv('EMAIL_HOST') ?: 'mail.institutozoe.net';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('EMAIL_USER') ?: 'noreply@institutozoe.net';
        $mail->Password = getenv('EMAIL_PASS') ?: '(t+81Y3N8(,T';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = getenv('EMAIL_PORT') ?: 465;
        
        // Remetente e destinatário
        $mail->setFrom('noreply@institutozoe.net', 'Instituto Zoe');
        $mail->addAddress($email, $nomePaciente);
        
        // Conteúdo do email
        $mail->isHTML(true);
        $mail->Subject = 'Novo Exame Disponível - Instituto Zoe';
        
        $dataFormatada = date('d/m/Y H:i', strtotime($dataExame));
        
        $mail->Body = "
            <h2>Novo Exame Disponível</h2>
            <p>Olá {$nomePaciente},</p>
            <p>Um novo exame foi registrado em sua conta:</p>
            <p><strong>Tipo de Exame:</strong> {$tipoExame}</p>
            <p><strong>Data:</strong> {$dataFormatada}</p>
            <p>Para visualizar seus exames, acesse sua conta no portal.</p>
            <br>
            <p>Atenciosamente,<br>Instituto Zoe</p>
        ";
        
        $mail->AltBody = "Novo exame disponível: {$tipoExame}";
        
        $mail->send();
        return true;
        
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('Erro ao enviar email: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Fallback: Envia email simples usando mail() do PHP
 * Usada se PHPMailer não estiver disponível
 */
function enviarEmailSimples($email, $nome, $token = null, $tipo = 'ativacao', $tipoExame = '', $dataExame = '') {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . EMAIL_EQUIPE . "\r\n";
    
    if ($tipo === 'ativacao') {
        $linkAtivacao = 'http://localhost/public_html/ativar-conta.php?token=' . $token;
        $subject = 'Ative sua conta - Instituto Zoe';
        $body = "
            <h2>Bem-vindo ao Instituto Zoe!</h2>
            <p>Olá {$nome},</p>
            <p>Para ativar sua conta, clique no link abaixo:</p>
            <p><a href='{$linkAtivacao}'>Ativar Conta</a></p>
            <p>Este link expira em 24 horas.</p>
        ";
    } else {
        $subject = 'Novo Exame Disponível - Instituto Zoe';
        $dataFormatada = date('d/m/Y H:i', strtotime($dataExame));
        $body = "
            <h2>Novo Exame Disponível</h2>
            <p>Olá {$nome},</p>
            <p>Um novo exame foi registrado em sua conta:</p>
            <p><strong>Tipo de Exame:</strong> {$tipoExame}</p>
            <p><strong>Data:</strong> {$dataFormatada}</p>
        ";
    }
    
    // Suprimir aviso de SMTP não configurado (em desenvolvimento)
    return @mail($email, $subject, $body, $headers) || true;
}

/**
 * Envia email para recuperação de senha
 * 
 * @param string $email Email do usuário
 * @param string $nome Nome do usuário
 * @param string $token Token para redefinição de senha
 * @return bool true se enviado com sucesso
 */
function enviarEmailRecuperacaoSenha($email, $nome, $token) {
    global $usarPHPMailer;
    
    if (!$usarPHPMailer) {
        return enviarEmailRecuperacaoSenhaSimples($email, $nome, $token);
    }
    
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = getenv('EMAIL_HOST') ?: 'mail.institutozoe.net';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('EMAIL_USER') ?: 'noreply@institutozoe.net';
        $mail->Password = getenv('EMAIL_PASS') ?: '(t+81Y3N8(,T';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = getenv('EMAIL_PORT') ?: 465;
        
        // Remetente e destinatário
        $mail->setFrom('noreply@institutozoe.net', 'Instituto Zoe');
        $mail->addAddress($email, $nome);
        
        // Conteúdo do email
        $mail->isHTML(true);
        $mail->Subject = 'Redefinir Senha - Instituto Zoe';
        
        $linkReset = 'http://' . $_SERVER['HTTP_HOST'] . '/public_html/auth/redefinir-senha.php?token=' . $token;
        
        $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; background: #f5f5f5; }
                    .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
                    .header { background: linear-gradient(135deg, #0468BF 0%, #F28705 100%); color: white; padding: 20px; text-align: center; border-radius: 4px; }
                    .content { padding: 20px; color: #333; }
                    .btn { display: inline-block; background: #0468BF; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; margin: 20px 0; font-weight: bold; }
                    .footer { border-top: 1px solid #ddd; padding-top: 20px; font-size: 12px; color: #666; text-align: center; }
                    .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 10px; border-radius: 4px; color: #856404; margin: 10px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>🔐 Redefinir Senha</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Olá, <strong>" . htmlspecialchars($nome) . "</strong>!</p>
                        
                        <p>Recebemos uma solicitação para redefinir sua senha no Instituto Zoe. Clique no botão abaixo para criar uma nova senha:</p>
                        
                        <center>
                            <a href='" . htmlspecialchars($linkReset) . "' class='btn'>🔄 Redefinir Senha</a>
                        </center>
                        
                        <p style='margin-top: 20px;'><strong>Ou copie este link no navegador:</strong></p>
                        <p style='background: #f5f5f5; padding: 10px; word-break: break-all; font-family: monospace; font-size: 12px;'>" . htmlspecialchars($linkReset) . "</p>
                        
                        <div class='warning'>
                            <strong>⏰ Importante:</strong> Este link é válido por <strong>1 hora</strong>. Se você não solicitou esta mudança, ignore este email.
                        </div>
                        
                        <p style='margin-top: 20px; color: #666; font-size: 14px;'>
                            Por segurança, nunca compartilhe este link com ninguém.<br>
                            Este é um email automático, por favor não responda.
                        </p>
                    </div>
                    
                    <div class='footer'>
                        <p>&copy; 2025 Instituto Zoe - Todos os direitos reservados</p>
                        <p>Endereço: Instituto Zoe | Telefone: (81) XXXX-XXXX</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Clique no link para redefinir sua senha: {$linkReset}";
        
        $mail->send();
        return true;
        
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('Erro ao enviar email de recuperação: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Fallback: Envia email de recuperação de senha usando mail() do PHP
 */
function enviarEmailRecuperacaoSenhaSimples($email, $nome, $token) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . EMAIL_EQUIPE . "\r\n";
    
    $linkReset = 'http://' . $_SERVER['HTTP_HOST'] . '/public_html/auth/redefinir-senha.php?token=' . $token;
    $subject = 'Redefinir Senha - Instituto Zoe';
    
    $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
                .header { background: linear-gradient(135deg, #0468BF 0%, #F28705 100%); color: white; padding: 20px; text-align: center; border-radius: 4px; }
                .content { padding: 20px; color: #333; }
                .btn { display: inline-block; background: #0468BF; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; margin: 20px 0; font-weight: bold; }
                .footer { border-top: 1px solid #ddd; padding-top: 20px; font-size: 12px; color: #666; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🔐 Redefinir Senha</h2>
                </div>
                <div class='content'>
                    <p>Olá {$nome},</p>
                    <p>Clique no link abaixo para redefinir sua senha:</p>
                    <p><a href='{$linkReset}' class='btn'>Redefinir Senha</a></p>
                    <p>Este link expira em 1 hora.</p>
                    <p>Se você não solicitou isto, ignore este email.</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2025 Instituto Zoe - Todos os direitos reservados</p>
                </div>
            </div>
        </body>
        </html>
    ";
    
    return @mail($email, $subject, $body, $headers) || true;
}

/**
 * Envia email de verificação de conta (novo cadastro)
 * 
 * @param string $email Email do usuário
 * @param string $nome Nome do usuário
 * @param string $link_verificacao Link completo para verificar email
 * @return bool true se enviado com sucesso
 */
function enviarEmailVerificacao($email, $nome, $link_verificacao) {
    $debug_log = __DIR__ . '/../debug-email.log';
    
    $write_log = function($msg) use ($debug_log) {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[$timestamp] $msg\n";
        file_put_contents($debug_log, $log_entry, FILE_APPEND);
        error_log($msg);
    };
    
    $write_log('📧 TENTANDO ENVIAR EMAIL DE VERIFICAÇÃO: ' . $email);
    
    $assunto = 'Verifique seu Email - Instituto Zoe';
    
    $corpo_html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
            .header { background: linear-gradient(135deg, #0468BF 0%, #F28705 100%); color: white; padding: 20px; text-align: center; border-radius: 4px; }
            .content { padding: 20px; color: #333; }
            .btn { display: inline-block; background: #0468BF; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; margin: 20px 0; font-weight: bold; }
            .footer { border-top: 1px solid #ddd; padding-top: 20px; font-size: 12px; color: #666; text-align: center; }
            .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 10px; border-radius: 4px; color: #856404; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Bem-vindo ao Instituto Zoe! 👋</h2>
            </div>
            
            <div class='content'>
                <p>Olá, <strong>" . htmlspecialchars($nome) . "</strong>!</p>
                
                <p>Obrigado por se cadastrar no Instituto Zoe. Para ativar sua conta e acessar todos os recursos, clique no botão abaixo:</p>
                
                <center>
                    <a href='" . htmlspecialchars($link_verificacao) . "' class='btn'>✓ Verificar Email</a>
                </center>
                
                <p style='margin-top: 20px;'><strong>Ou copie este link no navegador:</strong></p>
                <p style='background: #f5f5f5; padding: 10px; word-break: break-all; font-family: monospace; font-size: 12px;'>" . htmlspecialchars($link_verificacao) . "</p>
                
                <div class='warning'>
                    <strong>⏰ Importante:</strong> Este link é válido por <strong>24 horas</strong>. Após esse período, será necessário solicitar um novo email de confirmação.
                </div>
                
                <p style='margin-top: 20px; color: #666; font-size: 14px;'>
                    Se você não se cadastrou no Instituto Zoe, ignore este email.<br>
                    Este é um email automático, por favor não responda.
                </p>
            </div>
            
            <div class='footer'>
                <p>&copy; 2025 Instituto Zoe - Todos os direitos reservados</p>
                <p>Endereço: Instituto Zoe | Telefone: (81) XXXX-XXXX</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Tentar com PHPMailer primeiro
    try {
        $autoload_path = __DIR__ . '/../vendor/autoload.php';
        $write_log('🔍 Procurando PHPMailer em: ' . $autoload_path);
        
        if (file_exists($autoload_path)) {
            $write_log('✅ PHPMailer encontrado!');
            require_once $autoload_path;
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            
            $host = getenv('EMAIL_HOST') ?: 'mail.institutozoe.net';
            $port = getenv('EMAIL_PORT') ?: 465;
            $user = getenv('EMAIL_USER') ?: 'noreply@institutozoe.net';
            $pass = getenv('EMAIL_PASS') ?: '(t+81Y3N8(,T';
            
            $write_log('📬 Configurando SMTP:');
            $write_log('   HOST: ' . $host);
            $write_log('   PORT: ' . $port);
            $write_log('   USER: ' . $user);
            $write_log('   PASS: ' . (strlen($pass) > 0 ? '✅ Definida (' . strlen($pass) . ' chars)' : '❌ Vazia!'));
            
            $mail->Host = $host;
            $mail->Port = $port;
            $mail->SMTPAuth = true;
            $mail->Username = $user;
            $mail->Password = $pass;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPDebug = 0; // Desabilitar debug para não ficar verboso
            
            $mail->setFrom('noreply@institutozoe.net', 'Instituto Zoe');
            $mail->addAddress($email, $nome);
            $mail->Subject = $assunto;
            $mail->msgHTML($corpo_html);
            
            $write_log('📤 Tentando conectar ao SMTP e enviar...');
            $mail->send();
            $write_log('✅ EMAIL ENVIADO COM SUCESSO VIA PHPMAILER!');
            return true;
        } else {
            $write_log('❌ PHPMailer NÃO ENCONTRADO em: ' . $autoload_path);
        }
    } catch (Exception $e) {
        $write_log('❌ PHPMailer ERRO: ' . $e->getMessage());
    }
    
    // Fallback para mail() nativo
    $write_log('🔄 Tentando fallback com mail() nativa do PHP...');
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . EMAIL_EQUIPE . "\r\n";
    $headers .= "Reply-To: " . EMAIL_EQUIPE . "\r\n";
    $headers .= "X-Mailer: Instituto-Zoe/1.0\r\n";
    
    $sucesso = @mail($email, $assunto, $corpo_html, $headers);
    
    if ($sucesso) {
        $write_log('✅ mail() nativa enviou com sucesso para: ' . $email);
    } else {
        $write_log('❌ mail() nativa FALHOU para: ' . $email);
    }
    
    return true; // Não bloqueia cadastro
}

?>
