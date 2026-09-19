-- Sistema de Login + Gestão de Racks do Bloco B (UNESC)
-- Banco único: usuários do sistema de login e as tabelas do trabalho de modelagem

CREATE DATABASE IF NOT EXISTS sistema_login;
USE sistema_login;

-- ============================================================
-- Autenticação
-- ============================================================

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('creator', 'admin', 'guest') NOT NULL DEFAULT 'guest'
);

-- ============================================================
-- Gestão de Infraestrutura de Racks - Bloco B
-- ============================================================

CREATE TABLE IF NOT EXISTS ambientes (
    id_ambiente INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    andar INT NOT NULL
);

CREATE TABLE IF NOT EXISTS racks (
    id_rack INT AUTO_INCREMENT PRIMARY KEY,
    identificacao VARCHAR(50) NOT NULL,
    tamanho_us INT NOT NULL,
    data_instalacao DATE,
    id_ambiente INT NOT NULL,
    FOREIGN KEY (id_ambiente) REFERENCES ambientes(id_ambiente)
);

CREATE TABLE IF NOT EXISTS categorias_equipamento (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS fabricantes (
    id_fabricante INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    suporte_contato VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS equipamentos_rack (
    id_equipamento INT AUTO_INCREMENT PRIMARY KEY,
    id_rack INT NOT NULL,
    id_categoria INT NOT NULL,
    id_fabricante INT NOT NULL,
    quantidade_portas INT,
    FOREIGN KEY (id_rack) REFERENCES racks(id_rack),
    FOREIGN KEY (id_categoria) REFERENCES categorias_equipamento(id_categoria),
    FOREIGN KEY (id_fabricante) REFERENCES fabricantes(id_fabricante)
);

CREATE TABLE IF NOT EXISTS tecnicos (
    id_tecnico INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    matricula VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS manutencoes (
    id_manutencao INT AUTO_INCREMENT PRIMARY KEY,
    id_rack INT NOT NULL,
    id_tecnico INT NOT NULL,
    data_servico DATE NOT NULL,
    descricao TEXT NOT NULL,
    FOREIGN KEY (id_rack) REFERENCES racks(id_rack),
    FOREIGN KEY (id_tecnico) REFERENCES tecnicos(id_tecnico)
);

-- ============================================================
-- Dados iniciais (rode apenas uma vez)
-- ============================================================

INSERT INTO ambientes (nome, tipo, andar) VALUES
('Corredor Principal', 'Corredor', 1),
('Laboratório de Redes', 'Laboratório', 2),
('Sala dos Servidores', 'Sala', 1);

INSERT INTO racks (identificacao, tamanho_us, data_instalacao, id_ambiente) VALUES
('Rack-Mini-Corr1', 5, '2023-05-10', 1),
('Rack-Piso-LabRedes', 44, '2022-02-15', 2),
('Rack-Core-Servidores', 44, '2021-10-01', 3);

INSERT INTO categorias_equipamento (nome, descricao) VALUES
('Switch', 'Equipamento de interconexão de rede local'),
('Patch Panel', 'Painel de conexão para organização de cabos'),
('Roteador', 'Equipamento de roteamento entre redes'),
('Servidor', 'Computador de alto desempenho para serviços');

INSERT INTO fabricantes (nome, suporte_contato) VALUES
('Cisco', 'suporte@cisco.com'),
('Furukawa', '0800-furukawa'),
('MikroTik', 'support@mikrotik.com'),
('TP-Link', 'suporte.br@tp-link.com'),
('Dell', '0800-dell');

INSERT INTO equipamentos_rack (id_rack, id_categoria, id_fabricante, quantidade_portas) VALUES
(1, 1, 1, 24),
(1, 2, 2, 24),
(2, 3, 3, 10),
(2, 1, 4, 48),
(3, 4, 5, NULL);

INSERT INTO tecnicos (nome, matricula, email) VALUES
('Carlos Silva', 'TI-1001', 'carlos.silva@unesc.br'),
('Ana Paula', 'TI-1002', 'ana.paula@unesc.br');

INSERT INTO manutencoes (id_rack, id_tecnico, data_servico, descricao) VALUES
(1, 1, '2024-01-15', 'Organização de cabos e troca de patch cord defeituoso.'),
(2, 2, '2024-03-20', 'Atualização de firmware do switch principal.'),
(3, 1, '2024-08-05', 'Limpeza preventiva dos coolers do servidor.');
