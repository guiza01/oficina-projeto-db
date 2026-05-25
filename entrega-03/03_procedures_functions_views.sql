USE oficina_mecanica;

DROP VIEW IF EXISTS vw_clientes_historico;
CREATE VIEW vw_clientes_historico AS
SELECT
    c.cpf,
    c.nome,
    c.telefone,
    COUNT(DISTINCT v.id_veiculo) AS total_veiculos,
    COUNT(DISTINCT os.id_os) AS total_ordens,
    IFNULL(SUM(os.valor_total), 0) AS total_gasto
FROM cliente c
LEFT JOIN veiculo v ON c.cpf = v.cpf_cliente
LEFT JOIN ordem_servico os ON v.id_veiculo = os.id_veiculo
GROUP BY c.cpf, c.nome, c.telefone;

DROP VIEW IF EXISTS vw_ordens_servico_detalhes;
CREATE VIEW vw_ordens_servico_detalhes AS
SELECT
    os.id_os,
    os.data_abertura,
    os.status,
    os.valor_total,
    c.nome AS cliente_nome,
    v.placa,
    f.nome AS funcionario_nome
FROM ordem_servico os
INNER JOIN veiculo v ON os.id_veiculo = v.id_veiculo
INNER JOIN cliente c ON v.cpf_cliente = c.cpf
LEFT JOIN funcionario f ON os.id_funcionario = f.id_funcionario;

DROP VIEW IF EXISTS vw_pecas_estoque_baixo;
CREATE VIEW vw_pecas_estoque_baixo AS
SELECT
    p.id_peca,
    p.nome,
    e.quantidade,
    e.quantidade_minima,
    e.local_armazenamento
FROM peca p
INNER JOIN estoque e ON p.id_peca = e.id_peca
WHERE e.quantidade <= e.quantidade_minima;

DROP FUNCTION IF EXISTS fn_total_gasto_cliente;
DELIMITER $$
CREATE FUNCTION fn_total_gasto_cliente(p_cpf VARCHAR(11))
RETURNS DECIMAL(10, 2)
READS SQL DATA
BEGIN
    DECLARE v_total DECIMAL(10, 2);

    SELECT IFNULL(SUM(os.valor_total), 0)
    INTO v_total
    FROM ordem_servico os
    INNER JOIN veiculo v ON os.id_veiculo = v.id_veiculo
    WHERE v.cpf_cliente = p_cpf;

    RETURN v_total;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS fn_verificar_estoque_peca;
DELIMITER $$
CREATE FUNCTION fn_verificar_estoque_peca(p_id_peca INT, p_quantidade INT)
RETURNS TINYINT(1)
READS SQL DATA
BEGIN
    DECLARE v_quantidade INT;

    SELECT quantidade
    INTO v_quantidade
    FROM estoque
    WHERE id_peca = p_id_peca;

    IF v_quantidade >= p_quantidade THEN
        RETURN 1;
    END IF;

    RETURN 0;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS fn_descricao_status_os;
DELIMITER $$
CREATE FUNCTION fn_descricao_status_os(p_id_os INT)
RETURNS VARCHAR(100)
READS SQL DATA
BEGIN
    DECLARE v_status VARCHAR(50);

    SELECT status
    INTO v_status
    FROM ordem_servico
    WHERE id_os = p_id_os;

    IF v_status = 'Aberta' THEN
        RETURN 'Ordem aberta';
    ELSEIF v_status = 'Em Progresso' THEN
        RETURN 'Ordem em andamento';
    ELSEIF v_status = 'Concluida' THEN
        RETURN 'Ordem concluida';
    ELSEIF v_status = 'Cancelada' THEN
        RETURN 'Ordem cancelada';
    END IF;

    RETURN 'Status desconhecido';
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS fn_dias_manutencao;
DELIMITER $$
CREATE FUNCTION fn_dias_manutencao(p_id_os INT)
RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_dias INT;

    SELECT DATEDIFF(IFNULL(data_conclusao, CURDATE()), data_abertura)
    INTO v_dias
    FROM ordem_servico
    WHERE id_os = p_id_os;

    RETURN v_dias;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS fn_peca_critica;
DELIMITER $$
CREATE FUNCTION fn_peca_critica(p_id_peca INT)
RETURNS VARCHAR(50)
READS SQL DATA
BEGIN
    DECLARE v_quantidade INT;
    DECLARE v_minimo INT;

    SELECT quantidade, quantidade_minima
    INTO v_quantidade, v_minimo
    FROM estoque
    WHERE id_peca = p_id_peca;

    IF v_quantidade = 0 THEN
        RETURN 'CRITICO';
    ELSEIF v_quantidade < v_minimo THEN
        RETURN 'BAIXO';
    END IF;

    RETURN 'NORMAL';
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_criar_ordem_servico;
DELIMITER $$
CREATE PROCEDURE sp_criar_ordem_servico(
    IN p_id_veiculo INT,
    IN p_id_funcionario INT,
    OUT p_id_os INT,
    OUT p_mensagem VARCHAR(200)
)
BEGIN
    DECLARE v_total INT;

    SELECT COUNT(*)
    INTO v_total
    FROM veiculo
    WHERE id_veiculo = p_id_veiculo;

    IF v_total = 0 THEN
        SET p_id_os = NULL;
        SET p_mensagem = 'Veiculo nao encontrado';
    ELSE
        INSERT INTO ordem_servico (id_veiculo, id_funcionario, status)
        VALUES (p_id_veiculo, p_id_funcionario, 'Aberta');

        SET p_id_os = LAST_INSERT_ID();
        SET p_mensagem = 'Ordem criada com sucesso';
    END IF;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_adicionar_item_os;
DELIMITER $$
CREATE PROCEDURE sp_adicionar_item_os(
    IN p_id_os INT,
    IN p_id_servico INT,
    IN p_id_peca INT,
    IN p_quantidade INT,
    IN p_valor_unitario DECIMAL(10, 2),
    OUT p_id_item_os INT,
    OUT p_mensagem VARCHAR(200)
)
BEGIN
    DECLARE v_subtotal DECIMAL(10, 2);
    DECLARE v_estoque INT;
    DECLARE v_total INT;

    SELECT COUNT(*)
    INTO v_total
    FROM ordem_servico
    WHERE id_os = p_id_os;

    IF v_total = 0 THEN
        SET p_id_item_os = NULL;
        SET p_mensagem = 'Ordem de servico nao encontrada';
    ELSEIF p_id_peca IS NOT NULL THEN
        SELECT quantidade
        INTO v_estoque
        FROM estoque
        WHERE id_peca = p_id_peca;

        IF v_estoque < p_quantidade THEN
            SET p_id_item_os = NULL;
            SET p_mensagem = 'Estoque insuficiente';
        ELSE
            SET v_subtotal = p_quantidade * p_valor_unitario;

            INSERT INTO item_os (id_os, id_servico, id_peca, quantidade, valor_unitario, subtotal)
            VALUES (p_id_os, p_id_servico, p_id_peca, p_quantidade, p_valor_unitario, v_subtotal);

            SET p_id_item_os = LAST_INSERT_ID();
            SET p_mensagem = 'Item inserido';
            CALL sp_atualizar_valor_total_os(p_id_os);
        END IF;
    ELSE
        SET v_subtotal = p_quantidade * p_valor_unitario;

        INSERT INTO item_os (id_os, id_servico, id_peca, quantidade, valor_unitario, subtotal)
        VALUES (p_id_os, p_id_servico, p_id_peca, p_quantidade, p_valor_unitario, v_subtotal);

        SET p_id_item_os = LAST_INSERT_ID();
        SET p_mensagem = 'Item inserido';
        CALL sp_atualizar_valor_total_os(p_id_os);
    END IF;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_atualizar_valor_total_os;
DELIMITER $$
CREATE PROCEDURE sp_atualizar_valor_total_os(IN p_id_os INT)
BEGIN
    DECLARE v_total DECIMAL(10, 2);

    SELECT IFNULL(SUM(subtotal), 0)
    INTO v_total
    FROM item_os
    WHERE id_os = p_id_os;

    UPDATE ordem_servico
    SET valor_total = v_total
    WHERE id_os = p_id_os;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_concluir_ordem_servico;
DELIMITER $$
CREATE PROCEDURE sp_concluir_ordem_servico(
    IN p_id_os INT,
    OUT p_mensagem VARCHAR(200)
)
BEGIN
    DECLARE v_total INT;

    SELECT COUNT(*)
    INTO v_total
    FROM item_os
    WHERE id_os = p_id_os;

    IF v_total = 0 THEN
        SET p_mensagem = 'Ordem sem itens';
    ELSE
        UPDATE ordem_servico
        SET status = 'Concluida', data_conclusao = NOW()
        WHERE id_os = p_id_os;

        SET p_mensagem = 'Ordem concluida';
    END IF;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_cancelar_ordem_servico;
DELIMITER $$
CREATE PROCEDURE sp_cancelar_ordem_servico(
    IN p_id_os INT,
    IN p_motivo VARCHAR(200),
    OUT p_mensagem VARCHAR(200)
)
BEGIN
    UPDATE ordem_servico
    SET status = 'Cancelada',
        data_conclusao = NOW(),
        observacoes = p_motivo
    WHERE id_os = p_id_os;

    SET p_mensagem = 'Ordem cancelada';
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_atualizar_estoque_peca;
DELIMITER $$
CREATE PROCEDURE sp_atualizar_estoque_peca(
    IN p_id_peca INT,
    IN p_quantidade INT,
    OUT p_mensagem VARCHAR(200)
)
BEGIN
    UPDATE estoque
    SET quantidade = quantidade + p_quantidade,
        ultima_atualizacao = NOW()
    WHERE id_peca = p_id_peca;

    SET p_mensagem = 'Estoque atualizado';
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_relatorio_vendas_periodo;
DELIMITER $$
CREATE PROCEDURE sp_relatorio_vendas_periodo(
    IN p_data_inicio DATE,
    IN p_data_fim DATE
)
BEGIN
    SELECT
        DATE(data_abertura) AS data,
        COUNT(id_os) AS quantidade_os,
        SUM(valor_total) AS total_faturado
    FROM ordem_servico
    WHERE status = 'Concluida'
      AND DATE(data_abertura) BETWEEN p_data_inicio AND p_data_fim
    GROUP BY DATE(data_abertura)
    ORDER BY data DESC;
END$$
DELIMITER ;