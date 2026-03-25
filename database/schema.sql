-- Banco de Dados para Aplicativo de Controle Financeiro

-- Criação do banco de dados (se necessário)
-- CREATE DATABASE IF NOT EXISTS financas_app;
-- USE financas_app;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

-- Tabela de Categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    tipo ENUM('income', 'expense') NOT NULL,
    usuario_id INT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_tipo (usuario_id, tipo),
    INDEX idx_default (is_default, tipo)
);

-- Tabela de Transações
CREATE TABLE IF NOT EXISTS transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    tipo ENUM('income', 'expense') NOT NULL,
    data DATE NOT NULL,
    descricao TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    INDEX idx_usuario_data (usuario_id, data),
    INDEX idx_usuario_tipo (usuario_id, tipo),
    INDEX idx_categoria (categoria_id)
);

-- Inserção de categorias pré-estabelecidas (is_default = 1, usuario_id = NULL)
-- Categorias de Despesas
INSERT INTO categorias (nome, tipo, usuario_id, is_default) VALUES
('Alimentação', 'expense', NULL, TRUE),
('Moradia', 'expense', NULL, TRUE),
('Transporte', 'expense', NULL, TRUE),
('Saúde', 'expense', NULL, TRUE),
('Educação', 'expense', NULL, TRUE),
('Lazer', 'expense', NULL, TRUE),
('Vestuário', 'expense', NULL, TRUE),
('Impostos e Taxas', 'expense', NULL, TRUE),
('Dívidas e Empréstimos', 'expense', NULL, TRUE),
('Pets', 'expense', NULL, TRUE),
('Supermercado', 'expense', NULL, TRUE),
('Restaurantes', 'expense', NULL, TRUE),
('Aluguel', 'expense', NULL, TRUE),
('Condomínio', 'expense', NULL, TRUE),
('Luz', 'expense', NULL, TRUE),
('Água', 'expense', NULL, TRUE),
('Internet', 'expense', NULL, TRUE),
('Combustível', 'expense', NULL, TRUE),
('Transporte Público', 'expense', NULL, TRUE),
('Manutenção Veicular', 'expense', NULL, TRUE),
('Plano de Saúde', 'expense', NULL, TRUE),
('Consultas Médicas', 'expense', NULL, TRUE),
('Medicamentos', 'expense', NULL, TRUE),
('Cursos', 'expense', NULL, TRUE),
('Materiais Escolares', 'expense', NULL, TRUE),
('Cinema', 'expense', NULL, TRUE),
('Streaming', 'expense', NULL, TRUE),
('Hobbies', 'expense', NULL, TRUE),
('Roupas', 'expense', NULL, TRUE),
('Calçados', 'expense', NULL, TRUE),
('Imposto de Renda', 'expense', NULL, TRUE),
('IPVA', 'expense', NULL, TRUE),
('Empréstimos', 'expense', NULL, TRUE),
('Financiamentos', 'expense', NULL, TRUE),
('Ração', 'expense', NULL, TRUE),
('Veterinário', 'expense', NULL, TRUE);

-- Categorias de Receitas
INSERT INTO categorias (nome, tipo, usuario_id, is_default) VALUES
('Salário', 'income', NULL, TRUE),
('Freelance', 'income', NULL, TRUE),
('Investimentos', 'income', NULL, TRUE),
('Renda Extra', 'income', NULL, TRUE),
('Presentes', 'income', NULL, TRUE),
('Reembolsos', 'income', NULL, TRUE),
('Bonificação', 'income', NULL, TRUE),
('Dividendos', 'income', NULL, TRUE),
('Aluguel de Imóveis', 'income', NULL, TRUE),
('Vendas', 'income', NULL, TRUE);

-- View para facilitar consultas de categorias (padrão + personalizadas)
CREATE VIEW categorias_completas AS
SELECT 
    c.id,
    c.nome,
    c.tipo,
    c.usuario_id,
    c.is_default,
    c.data_criacao,
    u.nome as usuario_nome
FROM categorias c
LEFT JOIN usuarios u ON c.usuario_id = u.id;

-- View para dashboard resumido
CREATE VIEW dashboard_resumo AS
SELECT 
    t.usuario_id,
    u.nome as usuario_nome,
    u.email,
    SUM(CASE WHEN t.tipo = 'income' THEN t.valor ELSE 0 END) as total_receitas,
    SUM(CASE WHEN t.tipo = 'expense' THEN t.valor ELSE 0 END) as total_despesas,
    (SUM(CASE WHEN t.tipo = 'income' THEN t.valor ELSE 0 END) - SUM(CASE WHEN t.tipo = 'expense' THEN t.valor ELSE 0 END)) as saldo_atual,
    COUNT(t.id) as total_transacoes
FROM transacoes t
JOIN usuarios u ON t.usuario_id = u.id
GROUP BY t.usuario_id, u.nome, u.email;

-- View para relatórios por categoria
CREATE VIEW relatorio_categorias AS
SELECT 
    t.usuario_id,
    c.id as categoria_id,
    c.nome as categoria_nome,
    c.tipo as categoria_tipo,
    SUM(t.valor) as total_valor,
    COUNT(t.id) as total_transacoes,
    AVG(t.valor) as media_valor
FROM transacoes t
JOIN categorias c ON t.categoria_id = c.id
GROUP BY t.usuario_id, c.id, c.nome, c.tipo
ORDER BY total_valor DESC;