-- Mini ERP Database Structure
-- Criado para teste técnico Montink

SET FOREIGN_KEY_CHECKS = 0;

-- Limpar tabelas existentes
DROP TABLE IF EXISTS estoque_movimentacao;
DROP TABLE IF EXISTS pedido_itens;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS cupons;
DROP TABLE IF EXISTS estoque;
DROP TABLE IF EXISTS produto_variacoes;
DROP TABLE IF EXISTS produtos;

-- Tabela de Produtos
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nome (nome),
    INDEX idx_ativo (ativo),
    INDEX idx_preco (preco)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Variações de Produtos
CREATE TABLE produto_variacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    valor_adicional DECIMAL(10,2) DEFAULT 0.00,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    INDEX idx_produto_id (produto_id),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Estoque
CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    variacao_id INT NULL,
    quantidade INT NOT NULL DEFAULT 0,
    quantidade_minima INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (variacao_id) REFERENCES produto_variacoes(id) ON DELETE SET NULL,
    UNIQUE KEY unique_produto_variacao (produto_id, variacao_id),
    INDEX idx_quantidade (quantidade),
    INDEX idx_produto_variacao (produto_id, variacao_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Cupons
CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    tipo ENUM('percentual', 'valor_fixo') NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    valor_minimo_pedido DECIMAL(10,2) DEFAULT 0.00,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    limite_uso INT DEFAULT NULL,
    usado INT DEFAULT 0,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_ativo (ativo),
    INDEX idx_data_validade (data_inicio, data_fim),
    INDEX idx_usado (usado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_pedido VARCHAR(20) NOT NULL UNIQUE,
    subtotal DECIMAL(10,2) NOT NULL,
    desconto DECIMAL(10,2) DEFAULT 0.00,
    frete DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    cupom_id INT NULL,
    status ENUM('pendente', 'confirmado', 'processando', 'enviado', 'entregue', 'cancelado') DEFAULT 'pendente',
    
    -- Dados do cliente
    cliente_nome VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_telefone VARCHAR(20),
    
    -- Endereço de entrega
    cep VARCHAR(10) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    complemento VARCHAR(100),
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    
    observacoes TEXT,
    data_entrega_prevista DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cupom_id) REFERENCES cupons(id) ON DELETE SET NULL,
    INDEX idx_numero_pedido (numero_pedido),
    INDEX idx_status (status),
    INDEX idx_data_criacao (created_at),
    INDEX idx_cliente_email (cliente_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Itens do Pedido
CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    variacao_id INT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    preco_total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (variacao_id) REFERENCES produto_variacoes(id) ON DELETE SET NULL,
    INDEX idx_pedido_id (pedido_id),
    INDEX idx_produto_id (produto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Log de Estoque
CREATE TABLE estoque_movimentacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    variacao_id INT NULL,
    tipo ENUM('entrada', 'saida', 'ajuste') NOT NULL,
    quantidade_anterior INT NOT NULL,
    quantidade_movimentada INT NOT NULL,
    quantidade_atual INT NOT NULL,
    motivo VARCHAR(255),
    pedido_id INT NULL,
    usuario VARCHAR(100) DEFAULT 'sistema',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (variacao_id) REFERENCES produto_variacoes(id) ON DELETE SET NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
    INDEX idx_produto_variacao (produto_id, variacao_id),
    INDEX idx_data_movimentacao (created_at),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Inserir dados iniciais
INSERT INTO produtos (nome, preco, descricao) VALUES
('Smartphone Galaxy S23', 2499.90, 'Smartphone Samsung Galaxy S23 com 128GB de armazenamento, tela de 6.1", câmera tripla de 50MP'),
('Notebook Dell Inspiron', 3299.00, 'Notebook Dell Inspiron 15 com Intel Core i5, 8GB RAM, SSD 256GB, tela 15.6"'),
('Mouse Gamer RGB', 129.90, 'Mouse gamer com sensor óptico de alta precisão, iluminação RGB e 6 botões programáveis'),
('Teclado Mecânico', 299.90, 'Teclado mecânico para gamers com switches azuis, iluminação RGB e teclas anti-ghosting'),
('Fone Bluetooth Premium', 199.90, 'Fone de ouvido sem fio com cancelamento ativo de ruído, bateria 30h e qualidade Hi-Fi');

-- Inserir variações de produtos
INSERT INTO produto_variacoes (produto_id, nome, valor_adicional) VALUES
(1, '128GB - Preto', 0.00),
(1, '256GB - Preto', 300.00),
(1, '128GB - Branco', 0.00),
(1, '256GB - Branco', 300.00),
(1, '128GB - Azul', 50.00),
(2, '8GB RAM - 256GB SSD', 0.00),
(2, '16GB RAM - 512GB SSD', 800.00),
(2, '16GB RAM - 1TB SSD', 1200.00),
(3, 'Preto', 0.00),
(3, 'Branco', 0.00),
(3, 'RGB Especial', 50.00),
(4, 'Switch Azul', 0.00),
(4, 'Switch Vermelho', 0.00),
(4, 'Switch Marrom', 20.00);

-- Inserir estoque inicial
INSERT INTO estoque (produto_id, variacao_id, quantidade, quantidade_minima) VALUES
(1, 1, 25, 5),
(1, 2, 15, 5),
(1, 3, 20, 5),
(1, 4, 10, 5),
(1, 5, 18, 5),
(2, 6, 12, 3),
(2, 7, 8, 3),
(2, 8, 5, 3),
(3, 9, 50, 10),
(3, 10, 30, 10),
(3, 11, 15, 5),
(4, 12, 40, 8),
(4, 13, 35, 8),
(4, 14, 25, 8),
(5, NULL, 35, 10);

-- Inserir cupons de exemplo
INSERT INTO cupons (codigo, tipo, valor, valor_minimo_pedido, data_inicio, data_fim, limite_uso) VALUES
('DESCONTO10', 'percentual', 10.00, 100.00, '2025-01-01', '2025-12-31', 100),
('FRETE20', 'valor_fixo', 20.00, 50.00, '2025-01-01', '2025-06-30', 50),
('PRIMEIRA15', 'percentual', 15.00, 200.00, '2025-01-01', '2025-12-31', NULL),
('NATAL25', 'valor_fixo', 25.00, 150.00, '2025-12-01', '2025-12-31', 200),
('BEMVINDO', 'percentual', 5.00, 0.00, '2025-01-01', '2025-12-31', NULL);
