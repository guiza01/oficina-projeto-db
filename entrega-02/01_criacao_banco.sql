DROP DATABASE IF EXISTS oficina_mecanica;
CREATE DATABASE oficina_mecanica 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE oficina_mecanica;

CREATE TABLE cliente (
    cpf VARCHAR(11) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(11) NOT NULL,
    endereco VARCHAR(200),
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (cpf)
);

CREATE TABLE veiculo (
    id_veiculo INT NOT NULL AUTO_INCREMENT,
    cpf_cliente VARCHAR(11) NOT NULL,
    placa VARCHAR(10) NOT NULL UNIQUE,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    ano INT NOT NULL,
    cor VARCHAR(30),
    PRIMARY KEY (id_veiculo),
    FOREIGN KEY (cpf_cliente) REFERENCES cliente(cpf)
);

CREATE TABLE funcionario (
    id_funcionario INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(11) UNIQUE NOT NULL,
    especialidade VARCHAR(100),
    telefone VARCHAR(11),
    salario DECIMAL(10, 2) NOT NULL,
    data_admissao DATE DEFAULT CURDATE(),
    PRIMARY KEY (id_funcionario),
    CONSTRAINT chk_salario CHECK (salario > 0)
);

CREATE TABLE servico (
    id_servico INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT,
    valor_padrao DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (id_servico),
    CONSTRAINT chk_valor_servico CHECK (valor_padrao > 0)
);

CREATE TABLE peca (
    id_peca INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    valor_unitario DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (id_peca),
    CONSTRAINT chk_valor_peca CHECK (valor_unitario > 0)
);

CREATE TABLE estoque (
    id_estoque INT NOT NULL AUTO_INCREMENT,
    id_peca INT NOT NULL UNIQUE,
    quantidade INT NOT NULL DEFAULT 0,
    quantidade_minima INT DEFAULT 10,
    local_armazenamento VARCHAR(50),
    ultima_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_estoque),
    FOREIGN KEY (id_peca) REFERENCES peca(id_peca),
    CONSTRAINT chk_quantidade CHECK (quantidade >= 0)
);

CREATE TABLE ordem_servico (
    id_os INT NOT NULL AUTO_INCREMENT,
    id_veiculo INT NOT NULL,
    id_funcionario INT,
    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_conclusao DATETIME,
    status ENUM('Aberta', 'Em Progresso', 'Concluida', 'Cancelada') DEFAULT 'Aberta',
    valor_total DECIMAL(10, 2) DEFAULT 0,
    observacoes TEXT,
    PRIMARY KEY (id_os),
    FOREIGN KEY (id_veiculo) REFERENCES veiculo(id_veiculo),
    FOREIGN KEY (id_funcionario) REFERENCES funcionario(id_funcionario),
    CONSTRAINT chk_valor_total CHECK (valor_total >= 0)
);

CREATE TABLE item_os (
    id_item_os INT NOT NULL AUTO_INCREMENT,
    id_os INT NOT NULL,
    id_servico INT,
    id_peca INT,
    quantidade INT NOT NULL DEFAULT 1,
    valor_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (id_item_os),
    FOREIGN KEY (id_os) REFERENCES ordem_servico(id_os),
    FOREIGN KEY (id_servico) REFERENCES servico(id_servico),
    FOREIGN KEY (id_peca) REFERENCES peca(id_peca),
    CONSTRAINT chk_quantidade_item CHECK (quantidade > 0),
    CONSTRAINT chk_valor_unitario_item CHECK (valor_unitario > 0),
    CONSTRAINT chk_subtotal_calculo CHECK (subtotal = quantidade * valor_unitario)
);
