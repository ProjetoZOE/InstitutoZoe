# Instituto Zoe - Website

Bem-vindo ao repositório oficial do Instituto Zoe, dedicado ao desenvolvimento e manutenção do website institucional.

## 🚀 NOVIDADE: Sistema de Login com Painéis Baseados em Perfil

O website agora possui:
- ✅ **Sistema de Autenticação** com login/cadastro seguro
- ✅ **Painel de Controle** (Exclusivo para Administradores)
- ✅ **Painel de Usuário** (Para pacientes, responsáveis e funcionários)
- ✅ **Gerenciamento de Usuários** (Admin apenas)
- ✅ **Visualização de Exames** (Com download seguro)
- ✅ **Agendamentos** (Sistema em desenvolvimento)
- ✅ **Edição de Perfil**

**Comece agora**: 
1. Acesse `teste-config.php` para validar a configuração
2. Leia [PAINEL-USUARIOS-INSTRUCOES.md](PAINEL-USUARIOS-INSTRUCOES.md) para entender o fluxo de acesso

## Sobre o Instituto Zoe

O Instituto Zoe é uma organização comprometida em promover saúde, inclusão e desenvolvimento social para crianças, adolescentes e famílias em situação de vulnerabilidade. Atuamos com projetos, campanhas e serviços que transformam vidas por meio de atendimento multiprofissional, atividades esportivas, culturais e ações de conscientização.

## Objetivo deste Repositório

Este repositório tem como finalidade registrar todo o processo de criação e evolução do website do Instituto Zoe, incluindo:

- Documentação das etapas do projeto;
- Planejamento e estruturação do site;
- Design e protótipos;
- Desenvolvimento front-end;
- Implementação de funcionalidades (Backend - Autenticação, Painel de Controle);
- Testes e ajustes;
- Deploy e manutenção.

## 📁 Estrutura do Projeto (NOVA - v2.0)

### 🏠 Página Principal
- `index.php` — Homepage (na raiz do projeto)

### 🔐 Autenticação
- `auth/login.php` — Login e Cadastro
  - Email verification obrigatória
  - Reenvio de email se necessário

### 📊 Painéis (Dashboard)
- `dashboard/admin/` — Painel Administrativo
  - `index.php` — Dashboard Admin
  - `users.php` — Gerenciar Usuários
- `dashboard/user/` — Painel do Usuário
  - `index.php` — Dashboard
  - `profile.php` — Editar Perfil
  - `exams.php` — Consultar Exames
  - `appointments.php` — Agendamentos

### 📄 Páginas Públicas
- `public/pages/` — Páginas estáticas
  - `activities.php` — Atividades
  - `campaigns.php` — Campanhas
  - `health.php` — Saúde
  - `support.php` — Suporte
- `public/contact.php` — Contato

### ⚙️ Configuração
- `config/` — Arquivos sensíveis (protegidos por .htaccess)
  - `auth.php` — Autenticação e Session
  - `database.php` — Conexão com BD
  - `email.php` — Sistema de Email

### 📦 Recursos
- `includes/` — Componentes reutilizáveis
  - `navbar.php` — Menu de Navegação
  - `footer.php` — Rodapé
- `assets/` — Recursos estáticos
  - `css/style.css` — Estilos globais
  - `js/navbar.js` — JavaScript do menu
  - `images/` — Imagens (Ballet/, Comemorativas/, Multirão/, Palestra/)
  - `fonts/` — Tipografias

### 🔧 Scripts
- `scripts/setup/` — Configuração inicial
  - `create-admin.php` — Criar admin (executa uma vez)
  - `migrate.php` — Migrar banco de dados
- `scripts/test/` — Testes
  - `test-config.php` — Verificar configuração
  - `test-dashboard.php` — Testar dashboard

### 📚 Documentação
- `docs/` — Guias completos
  - `SECURITY.md` — Segurança implementada
  - `TESTING.md` — Guia de testes
  - `EMAIL-CONFIG.md` — Configuração de email
  - `REORGANIZACAO.md` — Detalhes da reorganização

## 🛠 Como Começar

### Pré-requisitos
- PHP 7.4+
- MySQL 5.7+
- Apache com mod_rewrite (para .htaccess)

### Setup Inicial

1. **Importe o banco de dados:**
   ```bash
   mysql -u root -p < teste_institutozoe.sql
   ```

2. **Teste a configuração:**
   Acesse no navegador: `http://localhost/repo/InstitutoZoe/scripts/test/test-config.php`
   
   Todos os testes devem passar com badges verdes ✓

3. **Crie o usuário admin (primeira vez):**
   Acesse: `http://localhost/repo/InstitutoZoe/scripts/setup/create-admin.php`
   
   Siga as instruções na tela

4. **Acesse o sistema:**
   - Homepage: `http://localhost/repo/InstitutoZoe/`
   - Login: `http://localhost/repo/InstitutoZoe/auth/login.php`

---

## 🔐 Segurança

A aplicação implementa múltiplas camadas de segurança:

- ✅ **Session Timeout**: Auto-logout após 15 min de inatividade
- ✅ **Email Verification**: Verificação obrigatória de email
- ✅ **Admin Protection**: Script de setup executa uma vez
- ✅ **Directory Protection**: .htaccess bloqueia acesso a `/config`, `/scripts`, `/includes`
- ✅ **Password Hashing**: Senhas com bcrypt
- ✅ **Prepared Statements**: Proteção contra SQL injection

**Leia:** [docs/SECURITY.md](docs/SECURITY.md) para detalhes completos

---

## 🧪 Testes

Consulte o guia completo em: [docs/TESTING.md](docs/TESTING.md)

**Testes rápidos:**
```bash
# 1. Verificar configuração
http://localhost/repo/InstitutoZoe/scripts/test/test-config.php

# 2. Testar fluxo de cadastro/login
# Acesse auth/login.php e siga os passos

# 3. Verificar session timeout
# Faça login e espere 15 minutos
```

---

## 📚 Documentação

- 📖 [SECURITY.md](docs/SECURITY.md) - Segurança implementada
- 📖 [TESTING.md](docs/TESTING.md) - Guia completo de testes
- 📖 [EMAIL-CONFIG.md](docs/EMAIL-CONFIG.md) - Configuração de email
- 📖 [REORGANIZACAO.md](docs/REORGANIZACAO.md) - Detalhes da reestruturação v2.0

---

## ✨ Funcionalidades Implementadas

### ✅ Autenticação
- Login seguro
- Cadastro com verificação de email
- Session timeout (15 min)
- Reenvio de email

### ✅ Painéis
- Painel Admin com gerencimento de usuários
- Painel de Usuário com dashboard pessoal
- Edição de perfil
- Visualização de exames

### ✅ Proteção
- Controle de acesso por perfil
- Bloqueio de diretórios sensíveis
- Senhas hasheadas
- Tokens com validade

### ⏳ Próximas Fases
- CSRF tokens em formulários
- Rate limiting para login
- Two-factor authentication
- Logging de atividades

## 🔐 Segurança

- ✅ Prepared Statements em todas as queries SQL
- ✅ Validação de entrada (trim, filter_var, htmlspecialchars)
- ✅ Password hashing com PASSWORD_BCRYPT
- ✅ Controle de acesso por perfil
- ✅ Proteção contra SQL injection
- ✅ Proteção contra XSS
- ✅ Sessão segura

## 📖 Documentação Completa

- **[CONFIGURACAO.md](CONFIGURACAO.md)** - Guia de configuração e setup
- **[IMPLEMENTACAO.md](IMPLEMENTACAO.md)** - Detalhes técnicos da implementação

## 📞 Contato

Para dúvidas ou sugestões, entre em contato com a equipe do Instituto Zoe pelo e-mail: equipezoe7@gmail.com

---

Obrigado por acompanhar o desenvolvimento do nosso website!

**Última Atualização**: 22/12/2025
**Status**: ✅ Pronto para Uso
