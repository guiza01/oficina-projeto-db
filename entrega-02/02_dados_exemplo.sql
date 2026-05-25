USE oficina_mecanica;

INSERT INTO cliente (cpf, nome, email, telefone, endereco) VALUES
('12345678901', 'João Silva Santos', 'joao@email.com', '87988776655', 'Rua A, 123 - Recife'),
('98765432101', 'Maria Oliveira Costa', 'maria@email.com', '87988776656', 'Avenida B, 456 - Olinda'),
('55544433322', 'Pedro Ferreira Lima', 'pedro@email.com', '87988776657', 'Rua C, 789 - Jaboatão'),
('11122233344', 'Ana Paula Mendes', 'ana@email.com', '87988776658', 'Avenida D, 321 - Recife'),
('44455566677', 'Carlos Roberto Silva', 'carlos@email.com', '87988776659', 'Rua E, 654 - Paulista');

INSERT INTO veiculo (cpf_cliente, placa, marca, modelo, ano, cor) VALUES
('12345678901', 'ABC1234', 'Fiat', 'Uno', 2015, 'Branco'),
('12345678901', 'XYZ9876', 'Volkswagen', 'Gol', 2018, 'Prata'),
('98765432101', 'DEF5678', 'Chevrolet', 'Cruze', 2017, 'Preto'),
('55544433322', 'GHI9012', 'Honda', 'Civic', 2019, 'Azul'),
('11122233344', 'JKL3456', 'Toyota', 'Corolla', 2020, 'Prata'),
('44455566677', 'MNO7890', 'Ford', 'Fiesta', 2016, 'Vermelho');

INSERT INTO funcionario (nome, cpf, especialidade, telefone, salario, data_admissao) VALUES
('João Mecânico Senior', '10011122233', 'Motor', '87987654321', 3500.00, '2022-01-15'),
('Carlos Especialista Suspensão', '20022233344', 'Suspensão', '87987654322', 3200.00, '2022-06-20'),
('Paulo Eletricista', '30033344455', 'Elétrica', '87987654323', 3000.00, '2023-01-10'),
('Roberto Generalista', '40044455566', 'Geral', '87987654324', 2800.00, '2023-03-05'),
('Fernando Pintor', '50055566677', 'Pintura', '87987654325', 2600.00, '2023-05-12');

INSERT INTO servico (nome, descricao, valor_padrao) VALUES
('Troca de Óleo', 'Troca de óleo do motor com filtro', 85.00),
('Alinhamento', 'Alinhamento de direção e suspensão', 150.00),
('Revisão Completa', 'Revisão geral do veículo', 250.00),
('Troca de Pneu', 'Troca de pneu ou reparo de câmara', 120.00),
('Limpeza de Combustor', 'Limpeza do sistema de combustão', 180.00),
('Balanceamento', 'Balanceamento de rodas', 100.00),
('Freios', 'Manutenção do sistema de freios', 200.00),
('Bateria', 'Substituição de bateria', 350.00);

INSERT INTO peca (nome, descricao, valor_unitario) VALUES
('Óleo Mineral 20W50', 'Óleo mineral para motor', 45.00),
('Filtro de Óleo', 'Filtro de óleo padrão', 25.00),
('Filtro de Ar', 'Filtro de ar do motor', 35.00),
('Vela de Ignição', 'Vela de ignição comum', 15.00),
('Pneu 185/65 R15', 'Pneu 185/65 R15', 180.00),
('Pastilha de Freio', 'Jogo de pastilhas de freio', 120.00),
('Disco de Freio', 'Disco de freio de reposição', 150.00),
('Bateria 60Ah', 'Bateria 60 Ampéres/hora', 300.00),
('Correia de Distribuição', 'Correia de distribuição', 200.00),
('Líquido de Arrefecimento', 'Líquido de arrefecimento 1L', 35.00);

INSERT INTO estoque (id_peca, quantidade, quantidade_minima, local_armazenamento) VALUES
(1, 50, 10, 'Prateleira A1'),
(2, 40, 15, 'Prateleira A2'),
(3, 35, 10, 'Prateleira B1'),
(4, 100, 20, 'Prateleira B2'),
(5, 15, 5, 'Armazém'),
(6, 25, 10, 'Prateleira C1'),
(7, 20, 8, 'Prateleira C2'),
(8, 12, 5, 'Cofre'),
(9, 8, 3, 'Armazém'),
(10, 60, 15, 'Prateleira D1');

INSERT INTO ordem_servico (id_veiculo, id_funcionario, data_abertura, data_conclusao, status, valor_total, observacoes) VALUES
(1, 1, '2026-05-10 08:00:00', '2026-05-10 10:30:00', 'Concluida', 130.00, 'Troca de óleo realizada com sucesso'),
(3, 2, '2026-05-09 09:00:00', '2026-05-09 12:00:00', 'Concluida', 250.00, 'Alinhamento completo e balanceamento'),
(2, 1, '2026-05-11 07:30:00', NULL, 'Em Progresso', 0.00, 'Revisão em andamento'),
(4, 3, '2026-05-08 14:00:00', '2026-05-08 16:00:00', 'Concluida', 200.00, 'Reparo elétrico simples'),
(5, 4, '2026-05-11 10:00:00', NULL, 'Aberta', 0.00, 'Agendada para revisão geral');

INSERT INTO item_os (id_os, id_servico, id_peca, quantidade, valor_unitario, subtotal) VALUES
(1, 1, NULL, 1, 85.00, 85.00),
(1, NULL, 1, 1, 45.00, 45.00),
(2, 2, NULL, 1, 150.00, 150.00),
(2, 6, NULL, 1, 100.00, 100.00),
(3, 3, NULL, 1, 250.00, 250.00),
(4, NULL, 4, 4, 15.00, 60.00),
(4, NULL, 3, 1, 35.00, 35.00),
(4, 5, NULL, 1, 180.00, 180.00);

ALTER TABLE veiculo AUTO_INCREMENT = 7;
ALTER TABLE funcionario AUTO_INCREMENT = 6;
ALTER TABLE servico AUTO_INCREMENT = 9;
ALTER TABLE peca AUTO_INCREMENT = 11;
