-- Criação da base de dados
CREATE DATABASE IF NOT EXISTS felixbus;
USE felixbus;

-- Tabela de perfis de utilizador
CREATE TABLE Perfil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(20) NOT NULL
);

-- Inserir perfis padrão
INSERT INTO Perfil (nome) VALUES 
('cliente'),
('funcionario'),
('admin');

-- Tabela de utilizadores
CREATE TABLE Utilizador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    perfil_id INT NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (perfil_id) REFERENCES Perfil(id)
);

-- Tabela de carteiras
CREATE TABLE Carteira (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT,
    saldo DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (utilizador_id) REFERENCES Utilizador(id)
);

-- Tabela de movimentos de carteira
CREATE TABLE MovimentoCarteira (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    valor DECIMAL(10,2) NOT NULL,
    tipo ENUM('adicionar', 'retirar', 'transferencia') NOT NULL,
    carteira_origem INT,
    carteira_destino INT,
    descricao TEXT,
    FOREIGN KEY (carteira_origem) REFERENCES Carteira(id),
    FOREIGN KEY (carteira_destino) REFERENCES Carteira(id)
);

-- Tabela de rotas
CREATE TABLE Rota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origem VARCHAR(100) NOT NULL,
    destino VARCHAR(100) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    capacidade INT NOT NULL
);

-- Tabela de viagens
CREATE TABLE Viagem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rota_id INT NOT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    FOREIGN KEY (rota_id) REFERENCES Rota(id)
);

-- Tabela de bilhetes
CREATE TABLE Bilhete (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    viagem_id INT NOT NULL,
    codigo_validacao VARCHAR(100) UNIQUE NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES Utilizador(id),
    FOREIGN KEY (viagem_id) REFERENCES Viagem(id)
);

-- Tabela de alertas
CREATE TABLE Alerta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    mensagem TEXT NOT NULL,
    data_inicio DATE,
    data_fim DATE,
    ativo BOOLEAN DEFAULT TRUE
);

-- Inserção dos utilizadores obrigatórios
-- Passwords em MD5 para simplicidade (não recomendado em produção)
INSERT INTO Utilizador (nome, email, password, perfil_id) VALUES
('Cliente Teste', 'cliente@felixbus.com', MD5('cliente'), 1),
('Funcionario Teste', 'funcionario@felixbus.com', MD5('funcionario'), 2),
('Admin Teste', 'admin@felixbus.com', MD5('admin'), 3);

-- Criação das carteiras associadas
INSERT INTO Carteira (utilizador_id, saldo) VALUES
(1, 0.00), -- cliente
(2, 0.00), -- funcionario
(3, 0.00), -- admin
(NULL, 0.00); -- carteira da empresa
