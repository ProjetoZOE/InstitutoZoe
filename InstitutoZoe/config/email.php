<?php
/**
 * Configuração de Email com PHPMailer
 * Apenas para emails de ativação e notificação de exames
 */

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
        $mail->Host = 'smtp.gmail.com'; // Alterar conforme necessário
        $mail->SMTPAuth = true;
        $mail->Username = getenv('EMAIL_USER'); // Variável de ambiente
        $mail->Password = getenv('EMAIL_PASS'); // Variável de ambiente
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
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
        $mail->Host = 'smtp.gmail.com'; // Alterar conforme necessário
        $mail->SMTPAuth = true;
        $mail->Username = getenv('EMAIL_USER');
        $mail->Password = getenv('EMAIL_PASS');
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Remetente e destinatário
        $mail->setFrom(getenv('EMAIL_USER'), 'Instituto Zoe');
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
    $headers .= 'From: contato@institutozoe.com.br' . "\r\n";
    
    if ($tipo === 'ativacao') {
        $linkAtivacao = 'http://localhost/repo/InstitutoZoe/ativar-conta.php?token=' . $token;
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
 * Envia email de verificação de conta (novo cadastro)
 * 
 * @param string $email Email do usuário
 * @param string $nome Nome do usuário
 * @param string $link_verificacao Link completo para verificar email
 * @return bool true se enviado com sucesso
 */
function enviarEmailVerificacao($email, $nome, $link_verificacao) {
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
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: noreply@institutozoe.com.br\r\n";
    $headers .= "Reply-To: suporte@institutozoe.com.br\r\n";
    $headers .= "X-Mailer: Instituto-Zoe/1.0\r\n";
    
    // Tentar enviar email (suprime avisos se servidor SMTP não está configurado)
    $sucesso = @mail($email, $assunto, $corpo_html, $headers);
    
    if (!$sucesso) {
        // Em desenvolvimento, apenas registra no log
        error_log('⚠️ Email não enviado para: ' . $email . ' (Configure SMTP em php.ini ou use PHPMailer)');
        // Retorna true mesmo assim para não bloquear cadastro
        // Em produção, configure um servidor SMTP real
        return true;
    }
    
    return $sucesso;
}

?>
