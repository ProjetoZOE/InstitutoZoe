# 🧪 Guia de Testes - Instituto Zoe

## Executando os Testes

### 1. Teste de Configuração
**Arquivo:** `scripts/test/test-config.php`
**Como acessar:** Navegador → `http://localhost/repo/InstitutoZoe/scripts/test/test-config.php`

Este script verifica:
- ✓ Conexão com banco de dados
- ✓ Tabelas obrigatórias existem
- ✓ Pastas de arquivo tem permissão
- ✓ Arquivos de configuração existem

**Esperado:** Todos os testes devem passar com badges verdes ✓

---

### 2. Teste de Dashboard
**Arquivo:** `scripts/test/test-dashboard.php`
**Como acessar:** Navegador → `http://localhost/repo/InstitutoZoe/scripts/test/test-dashboard.php`

Este script verifica:
- ✓ Funcionalidade de login
- ✓ Criação de sessão
- ✓ Redirecionamentos funcionam
- ✓ Permissões por perfil

---

## Testes Manuais

### Fluxo 1: Cadastro e Email Verification
1. Acesse `http://localhost/repo/InstitutoZoe/auth/login.php?tab=cadastro`
2. Preencha: nome, email, senha
3. Clique "Cadastrar"
4. **Esperado:** Mensagem de sucesso + email enviado
5. Verificar email em inbox ou Mailtrap
6. Clique no link de verificação
7. **Esperado:** Mensagem "Email verificado com sucesso!"
8. Faça login com credenciais
9. **Esperado:** Redirecionado para dashboard

### Fluxo 2: Session Timeout
1. Faça login normalmente
2. Aguarde 15 minutos sem atividade
3. Tente acessar qualquer página do dashboard
4. **Esperado:** Redirecionado para login com mensagem "Sessão expirada"

### Fluxo 3: Reenvio de Email
1. Cadastre novo usuário
2. Em `auth/login.php`, clique "Reenviar email de verificação"
3. Digite o email cadastrado
4. **Esperado:** Novo email com novo link enviado
5. Clique no novo link
6. **Esperado:** Email verificado, login permitido

### Fluxo 4: Admin Protection
1. Tente acessar `scripts/setup/create-admin.php` diretamente
2. **Esperado:** Primeira vez: cria admin, mostra mensagem ✓
3. Segunda tentativa: erro "Admin já foi criado"

### Fluxo 5: Autorização por Perfil
1. Faça login como PACIENTE
2. Tente acessar `dashboard/admin/index.php`
3. **Esperado:** Redirecionado para `dashboard/user/index.php`
4. Faça login como ADMIN
5. Acesse `dashboard/admin/index.php`
6. **Esperado:** Dashboard admin carrega com sucesso

---

## Testes de Segurança

### 1. Acesso Direto a /config
```
GET http://localhost/repo/InstitutoZoe/config/database.php
Esperado: 403 Forbidden (bloqueado por .htaccess)
```

### 2. Acesso Direto a /scripts
```
GET http://localhost/repo/InstitutoZoe/scripts/setup/create-admin.php
Esperado: 403 Forbidden (bloqueado por .htaccess)
```

### 3. Acesso Direto a /includes
```
GET http://localhost/repo/InstitutoZoe/includes/navbar.php
Esperado: 403 Forbidden (bloqueado por .htaccess)
```

### 4. SQL Injection
```
Email: admin@test.com' OR '1'='1
Esperado: Email inválido ou nenhum resultado (prepared statements protegem)
```

### 5. Força Bruta (Futuro)
```
Múltiplas tentativas de login falhadas
Esperado: Rate limiting bloqueia depois de X tentativas
(Ainda não implementado)
```

---

## Testes de Navegação

### Homepage
- ✓ Página carrega sem erros
- ✓ Menu de navegação aparece
- ✓ Links internos funcionam (#sobre, #serviços)
- ✓ Imagens carregam (assets/images/)

### Páginas Públicas
- ✓ `public/pages/activities.php` carrega
- ✓ `public/pages/campaigns.php` carrega
- ✓ `public/pages/health.php` carrega
- ✓ `public/contact.php` carrega

### Redirecionamentos Legados
- ✓ `index-login.php` → redireciona para `auth/login.php`
- ✓ `painel-controle.php` → redireciona para `dashboard/admin/index.php`
- ✓ `painel-usuario.php` → redireciona para `dashboard/user/index.php`

---

## Checklist Pré-Deploy

- [ ] Teste de configuração passou (verde ✓)
- [ ] Teste de dashboard passou (verde ✓)
- [ ] Cadastro funciona e email é recebido
- [ ] Login funciona para usuários verificados
- [ ] Session timeout funciona após 15 min
- [ ] Admin protection funciona
- [ ] Reenvio de email funciona
- [ ] Homepage carrega sem erros
- [ ] Redirecionamentos legados funcionam
- [ ] .htaccess bloqueia acesso a /config, /scripts, /includes
- [ ] Banco de dados foi migrado (`scripts/setup/migrate.php`)

---

## Comandos Úteis

### Resetar Banco de Dados
```bash
# Terminal na raiz do projeto
php scripts/setup/migrate.php
```

### Criar Admin
```bash
php scripts/setup/create-admin.php
```

### Ver Último Erro de Email
```bash
tail -f /var/log/mail.log
```

### Limpar Cache do Navegador
```
Ctrl+Shift+Delete (Windows/Linux)
Cmd+Shift+Delete (macOS)
```

---

## Reportando Bugs

Encontrou um problema? Abra uma issue com:
1. Descrição do problema
2. Passos para reproduzir
3. Comportamento esperado vs real
4. Screenshots (se aplicável)
5. Logs de erro (se houver)

Email: bugs@institutoszoe.com.br
