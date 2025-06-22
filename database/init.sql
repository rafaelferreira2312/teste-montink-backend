-- Configurações iniciais
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Usar o banco mini_erp
USE mini_erp;

-- Tabela de produtos (COM IMAGEM)
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(500) NULL,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nome (nome),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de variações de produtos
CREATE TABLE IF NOT EXISTS produto_variacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    valor_adicional DECIMAL(10,2) DEFAULT 0,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    INDEX idx_produto_id (produto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de estoque
CREATE TABLE IF NOT EXISTS estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    variacao_id INT NULL,
    quantidade INT NOT NULL DEFAULT 0,
    quantidade_minima INT DEFAULT 5,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_estoque (produto_id, variacao_id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (variacao_id) REFERENCES produto_variacoes(id) ON DELETE CASCADE,
    INDEX idx_quantidade (quantidade)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de cupons
CREATE TABLE IF NOT EXISTS cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    tipo ENUM('percentual', 'valor_fixo') NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    valor_minimo_pedido DECIMAL(10,2) DEFAULT 0,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    limite_uso INT NULL,
    usado INT DEFAULT 0,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_ativo (ativo),
    INDEX idx_data (data_inicio, data_fim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_pedido VARCHAR(20) UNIQUE NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    desconto DECIMAL(10,2) DEFAULT 0,
    frete DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'confirmado', 'processando', 'enviado', 'entregue', 'cancelado') DEFAULT 'pendente',
    cupom_id INT NULL,
    cliente_nome VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_telefone VARCHAR(20),
    cep VARCHAR(8) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(100),
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cupom_id) REFERENCES cupons(id),
    INDEX idx_numero_pedido (numero_pedido),
    INDEX idx_status (status),
    INDEX idx_cliente_email (cliente_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de itens do pedido
CREATE TABLE IF NOT EXISTS pedido_itens (
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
    FOREIGN KEY (variacao_id) REFERENCES produto_variacoes(id),
    INDEX idx_pedido_id (pedido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de movimentação de estoque
CREATE TABLE IF NOT EXISTS estoque_movimentacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    variacao_id INT NULL,
    tipo ENUM('entrada', 'saida') NOT NULL,
    quantidade_anterior INT NOT NULL,
    quantidade_movimentada INT NOT NULL,
    quantidade_atual INT NOT NULL,
    motivo VARCHAR(255),
    pedido_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (variacao_id) REFERENCES produto_variacoes(id),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    INDEX idx_produto_id (produto_id),
    INDEX idx_tipo (tipo),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir dados COM IMAGENS
INSERT IGNORE INTO produtos (id, nome, preco, descricao, imagem) VALUES
(1, 'Smartphone Galaxy S23', 2499.99, 'Smartphone top de linha com câmera de 200MP e tela AMOLED de 6.1 polegadas', 'https://a-static.mlcdn.com.br/800x560/samsung-galaxy-s23-fe-5g-smartphone-android-128gb-verde/magazineluiza/237985600/bba751160bb381b9764fb472eb7245f3.jpg'),
(2, 'Notebook Dell Inspiron', 3299.90, 'Notebook para trabalho e estudos com Intel i5, 8GB RAM e SSD 256GB', 'https://cdn.avaliado.com.br/media/p/notebook-inspiron-156-i15-i1100-a40p-8gb-ssd-256gb-dell-cor-preto_6ERQW27.jpg'),
(3, 'Mouse Gamer RGB', 149.90, 'Mouse gamer com sensor óptico de alta precisão e iluminação RGB personalizável', 'https://down-br.img.susercontent.com/file/br-11134258-7r98o-lzp49gh02mud40'),
(4, 'Teclado Mecânico', 299.90, 'Teclado mecânico com switches blue e retroiluminação LED branca', 'https://a-static.mlcdn.com.br/1500x1500/teclado-mecanico-gamer-usb-hyperx-preto-alloy-fps-rgb/magazineluiza/228818800/9140249e44bf5a1ed5975f4bcc45c6a0.jpg'),
(5, 'Fone Bluetooth Premium', 599.90, 'Fone de ouvido sem fio com cancelamento de ruído ativo e bateria de 30h', 'https://http2.mlstatic.com/D_NQ_NP_663506-MLU77144327592_062024-O.webp');

-- Inserir variações para o smartphone
INSERT IGNORE INTO produto_variacoes (id, produto_id, nome, valor_adicional) VALUES
(1, 1, '128GB - Preto', 0),
(2, 1, '256GB - Preto', 300),
(3, 1, '256GB - Branco', 300),
(4, 1, '512GB - Preto', 700),
(5, 1, '512GB - Branco', 700);

-- Inserir estoque inicial
INSERT IGNORE INTO estoque (produto_id, variacao_id, quantidade, quantidade_minima) VALUES
(1, 1, 15, 5),
(1, 2, 10, 3),
(1, 3, 8, 3),
(1, 4, 5, 2),
(1, 5, 3, 2),
(2, NULL, 12, 3),
(3, NULL, 25, 10),
(4, NULL, 18, 5),
(5, NULL, 20, 5);

-- Inserir cupons de exemplo
INSERT IGNORE INTO cupons (id, codigo, tipo, valor, valor_minimo_pedido, data_inicio, data_fim, limite_uso) VALUES
(1, 'DESCONTO10', 'percentual', 10.00, 100.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 100),
(2, 'FRETE20', 'valor_fixo', 20.00, 0.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 500),
(3, 'PRIMEIRA15', 'percentual', 15.00, 200.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), NULL),
(4, 'NATAL25', 'valor_fixo', 25.00, 150.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 45 DAY), 200),
(5, 'BEMVINDO', 'percentual', 5.00, 0.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 365 DAY), NULL);

SET FOREIGN_KEY_CHECKS = 1;
