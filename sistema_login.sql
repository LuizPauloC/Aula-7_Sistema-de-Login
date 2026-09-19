-- Aula 7 - Autenticação e Sessões no PHP
-- Banco de dados para o sistema de login

CREATE DATABASE IF NOT EXISTS sistema_login;
USE sistema_login;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('creator', 'admin', 'guest') NOT NULL DEFAULT 'guest'
);
