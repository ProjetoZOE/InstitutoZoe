# 🔒 Documentação de Segurança - Instituto Zoe

## Implementações de Segurança

### 1. Session Timeout (15 minutos)
**Arquivo:** `config/auth.php`

- Sessões expiram automaticamente após 15 minutos de inatividade
- Usuário é desconectado e redirecionado para página de login
- Flag: `$_SESSION['ultimo_ativo']` rastreia última atividade

**Como funciona:**
```php
$session_timeout = 15 * 60; // 15 minutos
if (isset($_SESSION['ultimo_ativo']) && (time() - $_SESSION['ultimo_ativo']) > $session_timeout) {
    destruirSessao(); // Faz logout automático
}
```

---

### 2. Proteção do Admin Setup (init-admin.php)
**Arquivo:** `scripts/setup/create-admin.php`

- Script só executa uma vez através de arquivo flag: `.admin_criado`
- Após primeira execução, script não pode ser rodado novamente
- Protege contra criação não autorizada de múltiplas contas admin

**Proteção:**
```php
if (file_exists('.admin_criado')) {
    die('❌ Admin já foi criado. Remova o arquivo .admin_criado para resetar.');
}
```

---

### 3. Email Verification Obrigatório
**Arquivo:** `config/email.php`

- Usuários devem verificar email antes de fazer login
- Token único com validade de 24 horas
- Link de verificação: `auth/login.php?verificar_email=1&token=<token>`

**Fluxo:**
1. Usuário se registra
2. Email de verificação é enviado
3. Usuário clica no link com token único
4. Email é marcado como verificado no banco
5. Login só é permitido após verificação

**Reenvio de Email:**
- Interface para reenviar email se não foi recebido
- Novo token é gerado se necessário

---

### 4. Proteção de Diretórios com .htaccess
**Arquivos:**
- `config/.htaccess` - Bloqueia acesso direto a PHP em /config
- `scripts/.htaccess` - Bloqueia acesso direto a PHP em /scripts
- `includes/.htaccess` - Bloqueia acesso direto a PHP em /includes

**Comportamento:**
```apache
<FilesMatch "\.php$">
    Deny from all
</FilesMatch>
```

---

### 5. Autenticação e Autorização
**Arquivo:** `config/auth.php`

- Função `verificarAutenticacao()` valida se usuário está logado
- Função `obterDadosUsuario()` retorna dados do usuário autenticado
- Perfis: ADMIN, PACIENTE, PROFISSIONAL (MÉDICO, ENFERMEIRA, etc)

**Verificação de Autorização:**
```php
if ($_SESSION['usuario_perfil'] !== 'ADMIN') {
    header('Location: ../dashboard/user/index.php');
    exit;
}
```

---

### 6. Hash de Senhas com bcrypt
**Codificação:**
- Senhas são hasheadas com `PASSWORD_BCRYPT`
- Verificação com `password_verify()`
- Nunca armazenar senhas em texto plano

---

## Estrutura de Diretórios

```
InstitutoZoe/
├── auth/                    # Autenticação
│   └── login.php           # Login e cadastro
├── config/                 # Configurações sensíveis
│   ├── .htaccess          # Bloqueio de acesso
│   ├── auth.php           # Middleware de autenticação
│   ├── database.php       # Conexão com BD
│   └── email.php          # Configuração de email
├── dashboard/             # Painéis de controle
│   ├── admin/             # Admin only
│   │   ├── index.php      # Painel admin
│   │   └── users.php      # Gerenciar usuários
│   └── user/              # Usuários autenticados
│       ├── index.php      # Dashboard
│       ├── profile.php    # Perfil
│       ├── exams.php      # Exames
│       └── appointments.php # Agendamentos
├── includes/              # Componentes reutilizáveis
│   ├── .htaccess         # Bloqueio de acesso
│   ├── navbar.php        # Menu de navegação
│   └── footer.php        # Rodapé
├── scripts/              # Scripts de sistema
│   ├── .htaccess        # Bloqueio de acesso
│   ├── setup/           # Setup inicial
│   │   ├── create-admin.php    # Criar admin
│   │   └── migrate.php         # Migrações
│   └── test/            # Testes
│       ├── test-config.php     # Teste de config
│       └── test-dashboard.php  # Teste dashboard
├── public/              # Páginas públicas
│   ├── pages/           # Páginas estáticas
│   ├── contact.php      # Contato
│   └── (outras páginas)
├── assets/              # Recursos estáticos
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── navbar.js
│   ├── images/
│   └── fonts/
└── docs/                # Documentação
```

---

## Checklist de Segurança

- [x] Session timeout implementado
- [x] Email verification obrigatória
- [x] Admin protection com flag file
- [x] .htaccess para diretórios sensíveis
- [x] Senhas com bcrypt
- [x] Autorização por perfil

---

## Próximas Melhorias

- [ ] CSRF tokens em formulários
- [ ] Rate limiting para login
- [ ] Logging de atividades
- [ ] Two-factor authentication (2FA)
- [ ] IP whitelist para admin
- [ ] HTTPS obrigatório
- [ ] Content Security Policy (CSP)

---

## Contato para Suporte

Para dúvidas sobre segurança, contate: suporte@institutoszoe.com.br
