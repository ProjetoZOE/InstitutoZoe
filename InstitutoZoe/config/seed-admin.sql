-- Script para inserir usuário ADMIN padrão (use antes de usar o sistema)
-- Email: admin@institutozoe.com.br
-- Senha: Admin@123

INSERT INTO usuario (email, senha_hash, perfil, ativo) 
VALUES (
    'admin@institutozoe.com.br',
    '$2y$10$7.ZPNz7r1vWvDNP6eIp8Fu9H5Mv8VQa9x6xQqQqI8vIlUVB.pJyZm',
    'ADMIN',
    1
);

-- Obtém o ID do usuário inserido (ajuste de acordo com o seu banco)
-- Depois use este comando para vincular a uma pessoa:
-- INSERT INTO pessoa (nome, id_usuario, ativo) VALUES ('Administrador', ID_DO_USUARIO, 1);
