-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS cakee_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE cakee_db;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de produtos
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Itens do pedido (muitos-para-muitos)
CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

-- Tabela de logs
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    acao VARCHAR(255),
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Trigger de log para novo produto
DELIMITER //
CREATE TRIGGER log_novo_produto
AFTER INSERT ON produtos
FOR EACH ROW
BEGIN
    INSERT INTO logs (acao) VALUES (CONCAT('Novo produto cadastrado: ', NEW.nome));
END;
//
DELIMITER ;

-- Trigger de log para novo usuário
DELIMITER //
CREATE TRIGGER log_novo_usuario
AFTER INSERT ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO logs (acao) VALUES (CONCAT('Novo usuário registrado: ', NEW.email));
END;
//
DELIMITER ;

-- Trigger de log para novo pedido
DELIMITER //
CREATE TRIGGER log_novo_pedido
AFTER INSERT ON pedidos
FOR EACH ROW
BEGIN
    INSERT INTO logs (acao) VALUES (CONCAT('Novo pedido criado: ID ', NEW.id));
END;
//
DELIMITER ;
