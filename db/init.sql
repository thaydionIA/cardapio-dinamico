-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS cardapio_dinamico;
USE cardapio_dinamico;

-- Tabela para armazenar os produtos do cardápio
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    categoria ENUM('entradas', 'principais', 'bebidas', 'sobremesas') NOT NULL,
    imagem VARCHAR(255) DEFAULT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela para armazenar os usuários administradores
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserindo um usuário administrador inicial
INSERT INTO usuarios (nome, email, senha) VALUES
('Administrador', 'admin@example.com', MD5('senha123'));
