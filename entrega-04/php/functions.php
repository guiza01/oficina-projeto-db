<?php

require_once 'Database.php';

function obter_clientes()
{
    $db = Database::getInstance();
    $sql = "SELECT * FROM cliente ORDER BY nome ASC";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function obter_cliente_por_cpf($cpf)
{
    $db = Database::getInstance();
    $cpf = preg_replace('/\D/', '', $cpf);
    $cpf = $db->escape($cpf);
    $sql = "SELECT * FROM cliente WHERE cpf = '$cpf'";
    $result = $db->query($sql);
    return $result->fetch_assoc();
}

function criar_cliente($cpf, $nome, $email, $telefone, $endereco)
{
    $db = Database::getInstance();
    $cpf = preg_replace('/\D/', '', $cpf);
    $telefone = preg_replace('/\D/', '', $telefone);
    $cpf = $db->escape($cpf);
    $nome = $db->escape($nome);
    $email = $db->escape($email);
    $telefone = $db->escape($telefone);
    $endereco = $db->escape($endereco);

    $sql = "INSERT INTO cliente (cpf, nome, email, telefone, endereco)
            VALUES ('$cpf', '$nome', '$email', '$telefone', '$endereco')";

    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Cliente criado com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao criar cliente: ' . $db->error()];
    }
}

function atualizar_cliente($cpf, $nome, $email, $telefone, $endereco)
{
    $db = Database::getInstance();
    $cpf = preg_replace('/\D/', '', $cpf);
    $telefone = preg_replace('/\D/', '', $telefone);
    $cpf = $db->escape($cpf);
    $nome = $db->escape($nome);
    $email = $db->escape($email);
    $telefone = $db->escape($telefone);
    $endereco = $db->escape($endereco);

    $sql = "UPDATE cliente SET nome = '$nome', email = '$email', telefone = '$telefone', endereco = '$endereco' WHERE cpf = '$cpf'";

    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Cliente atualizado com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao atualizar cliente'];
    }
}

function deletar_cliente($cpf)
{
    $db = Database::getInstance();
    $cpf = preg_replace('/\D/', '', $cpf);
    $cpf = $db->escape($cpf);
    $sql = "DELETE FROM cliente WHERE cpf = '$cpf'";

    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Cliente deletado com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao deletar cliente'];
    }
}

function obter_veiculos($cpf_cliente = null)
{
    $db = Database::getInstance();
    $sql = "SELECT v.*, c.nome as cliente_nome FROM veiculo v 
            INNER JOIN cliente c ON v.cpf_cliente = c.cpf";

    if ($cpf_cliente) {
        $cpf_cliente = $db->escape($cpf_cliente);
        $sql .= " WHERE v.cpf_cliente = '$cpf_cliente'";
    }

    $sql .= " ORDER BY v.marca ASC";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function obter_funcionarios()
{
    $db = Database::getInstance();
    $sql = "SELECT * FROM funcionario ORDER BY nome ASC";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function criar_funcionario($nome, $cpf, $especialidade = null, $telefone = null, $salario = 0, $data_admissao = null)
{
    $db = Database::getInstance();
    $nome = $db->escape($nome);
    $cpf = preg_replace('/\D/', '', $cpf);
    $cpf = $db->escape($cpf);
    $especialidade = $especialidade ? $db->escape($especialidade) : NULL;
    $telefone = $telefone ? $db->escape(preg_replace('/\D/', '', $telefone)) : NULL;
    $salario = floatval($salario);
    $data_admissao = $data_admissao ? "'" . $db->escape($data_admissao) . "'" : "NULL";

    $sql = "INSERT INTO funcionario (nome, cpf, especialidade, telefone, salario, data_admissao) VALUES ('$nome', '$cpf', " . ($especialidade ? "'$especialidade'" : "NULL") . ", " . ($telefone ? "'$telefone'" : "NULL") . ", $salario, $data_admissao)";

    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Funcionário criado com sucesso', 'id' => $db->lastInsertId()];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao criar funcionário: ' . $db->error()];
    }
}

function atualizar_funcionario($id_funcionario, $nome, $cpf, $especialidade = null, $telefone = null, $salario = 0, $data_admissao = null)
{
    $db = Database::getInstance();
    $id = intval($id_funcionario);
    $nome = $db->escape($nome);
    $cpf = preg_replace('/\D/', '', $cpf);
    $cpf = $db->escape($cpf);
    $especialidade = $especialidade ? $db->escape($especialidade) : NULL;
    $telefone = $telefone ? $db->escape(preg_replace('/\D/', '', $telefone)) : NULL;
    $salario = floatval($salario);
    $data_admissao = $data_admissao ? "'" . $db->escape($data_admissao) . "'" : "NULL";

    $sql = "UPDATE funcionario SET nome = '$nome', cpf = '$cpf', especialidade = " . ($especialidade ? "'$especialidade'" : "NULL") . ", telefone = " . ($telefone ? "'$telefone'" : "NULL") . ", salario = $salario, data_admissao = $data_admissao WHERE id_funcionario = $id";

    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Funcionário atualizado com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao atualizar funcionário: ' . $db->error()];
    }
}

function deletar_funcionario($id_funcionario)
{
    $db = Database::getInstance();
    $id = intval($id_funcionario);
    $sql = "DELETE FROM funcionario WHERE id_funcionario = $id";
    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Funcionário removido com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao remover funcionário: ' . $db->error()];
    }
}

function obter_veiculo($id)
{
    $db = Database::getInstance();
    $id = intval($id);
    $sql = "SELECT * FROM veiculo WHERE id_veiculo = $id";
    $result = $db->query($sql);
    return $result->fetch_assoc();
}

function criar_veiculo($cpf_cliente, $placa, $marca, $modelo, $ano, $cor)
{
    $db = Database::getInstance();
    $cpf_cliente = $db->escape($cpf_cliente);
    $placa = $db->escape($placa);
    $marca = $db->escape($marca);
    $modelo = $db->escape($modelo);
    $ano = intval($ano);
    $cor = $db->escape($cor);

    $sql = "INSERT INTO veiculo (cpf_cliente, placa, marca, modelo, ano, cor)
            VALUES ('$cpf_cliente', '$placa', '$marca', '$modelo', $ano, '$cor')";

    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Veículo cadastrado com sucesso', 'id' => $db->lastInsertId()];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao cadastrar veículo: ' . $db->error()];
    }
}

function editar_veiculo($id_veiculo, $cpf_cliente, $placa, $marca, $modelo, $ano, $cor)
{
    $db = Database::getInstance();
    $id_veiculo = intval($id_veiculo);
    $cpf_cliente = $db->escape($cpf_cliente);
    $placa = $db->escape($placa);
    $marca = $db->escape($marca);
    $modelo = $db->escape($modelo);
    $ano = intval($ano);
    $cor = $db->escape($cor);

    $sql = "UPDATE veiculo SET cpf_cliente = '$cpf_cliente', placa = '$placa', marca = '$marca', modelo = '$modelo', ano = $ano, cor = '$cor' WHERE id_veiculo = $id_veiculo";

    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Veículo atualizado com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao atualizar veículo: ' . $db->error()];
    }
}

function remover_veiculo($id_veiculo)
{
    $db = Database::getInstance();
    $id_veiculo = intval($id_veiculo);

    $sql_check = "SELECT COUNT(*) as total FROM ordem_servico WHERE id_veiculo = $id_veiculo";
    $result = $db->query($sql_check);
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        return ['sucesso' => false, 'mensagem' => 'Não é possível remover veículo que está em uso em ordens de serviço'];
    }

    $sql = "DELETE FROM veiculo WHERE id_veiculo = $id_veiculo";
    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Veículo removido com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao remover veículo: ' . $db->error()];
    }
}




function criar_ordem_servico($id_veiculo, $id_funcionario = null)
{
    $db = Database::getInstance();
    $id_veiculo = intval($id_veiculo);
    $id_funcionario = $id_funcionario ? intval($id_funcionario) : 'NULL';

    $sql = "INSERT INTO ordem_servico (id_veiculo, id_funcionario, status)
            VALUES ($id_veiculo, $id_funcionario, 'Aberta')";

    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'OS criada com sucesso', 'id' => $db->lastInsertId()];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao criar OS'];
    }
}

function obter_ordens_servico($filtro = null)
{
    $db = Database::getInstance();
    $sql = "SELECT * FROM vw_ordens_servico_detalhes WHERE 1=1";

    if ($filtro === 'abertas') {
        $sql .= " AND status = 'Aberta'";
    } elseif ($filtro === 'progresso') {
        $sql .= " AND status = 'Em Progresso'";
    } elseif ($filtro === 'concluidas') {
        $sql .= " AND status = 'Concluida'";
    }

    $sql .= " ORDER BY data_abertura DESC";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function obter_ordem_servico($id)
{
    $db = Database::getInstance();
    $id = intval($id);
    $sql = "SELECT * FROM vw_ordens_servico_detalhes WHERE id_os = $id";
    $result = $db->query($sql);
    return $result->fetch_assoc();
}

function atualizar_status_os($id_os, $novo_status)
{
    $db = Database::getInstance();
    $id_os = intval($id_os);
    $novo_status = $db->escape($novo_status);


    if ($novo_status === 'Concluida') {
        $sql = "UPDATE ordem_servico SET status = '$novo_status', data_conclusao = NOW() WHERE id_os = $id_os";
    } else {
        $sql = "UPDATE ordem_servico SET status = '$novo_status', data_conclusao = NULL WHERE id_os = $id_os";
    }

    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Status atualizado com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao atualizar status: ' . $db->error()];
    }
}

function deletar_ordem_servico($id_os)
{
    $db = Database::getInstance();
    $id_os = intval($id_os);

    $sql_itens = "DELETE FROM item_os WHERE id_os = $id_os";
    if (!$db->query($sql_itens)) {
        return ['sucesso' => false, 'mensagem' => 'Erro ao remover itens da OS: ' . $db->error()];
    }

    $sql = "DELETE FROM ordem_servico WHERE id_os = $id_os";
    if ($db->query($sql)) {
        return ['sucesso' => true, 'mensagem' => 'Ordem de serviço deletada com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao deletar ordem de serviço: ' . $db->error()];
    }
}




function obter_itens_os($id_os)
{
    $db = Database::getInstance();
    $id_os = intval($id_os);
    $sql = "SELECT * FROM vw_item_os_detalhes WHERE id_os = $id_os";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function adicionar_item_os_servico($id_os, $id_servico, $quantidade = 1)
{
    $db = Database::getInstance();
    $id_os = intval($id_os);
    $id_servico = intval($id_servico);
    $quantidade = intval($quantidade);

    $sql_valor = "SELECT valor_padrao FROM servico WHERE id_servico = $id_servico";
    $result = $db->query($sql_valor);
    $servico = $result->fetch_assoc();
    $valor_unitario = $servico['valor_padrao'];
    $subtotal = $quantidade * $valor_unitario;

    $sql = "INSERT INTO item_os (id_os, id_servico, id_peca, quantidade, valor_unitario, subtotal)
            VALUES ($id_os, $id_servico, NULL, $quantidade, $valor_unitario, $subtotal)";

    if ($db->query($sql)) {
        atualizar_valor_total_os($id_os);
        return ['sucesso' => true, 'mensagem' => 'Serviço adicionado'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao adicionar serviço'];
    }
}

function adicionar_item_os_peca($id_os, $id_peca, $quantidade = 1)
{
    $db = Database::getInstance();
    $id_os = intval($id_os);
    $id_peca = intval($id_peca);
    $quantidade = intval($quantidade);

    $sql_estoque = "SELECT quantidade FROM estoque WHERE id_peca = $id_peca";
    $result = $db->query($sql_estoque);
    $estoque = $result->fetch_assoc();

    if ($estoque['quantidade'] < $quantidade) {
        return ['sucesso' => false, 'mensagem' => 'Estoque insuficiente'];
    }

    $sql_valor = "SELECT valor_unitario FROM peca WHERE id_peca = $id_peca";
    $result = $db->query($sql_valor);
    $peca = $result->fetch_assoc();
    $valor_unitario = $peca['valor_unitario'];
    $subtotal = $quantidade * $valor_unitario;

    $sql = "INSERT INTO item_os (id_os, id_servico, id_peca, quantidade, valor_unitario, subtotal)
            VALUES ($id_os, NULL, $id_peca, $quantidade, $valor_unitario, $subtotal)";

    if ($db->query($sql)) {

        $novo_estoque = intval($estoque['quantidade']) - $quantidade;
        $sql_atualiza = "UPDATE estoque SET quantidade = $novo_estoque WHERE id_peca = $id_peca";
        $db->query($sql_atualiza);

        atualizar_valor_total_os($id_os);
        return ['sucesso' => true, 'mensagem' => 'Peça adicionada'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao adicionar peça'];
    }
}

function editar_item_os_quantidade($id_item_os, $nova_quantidade)
{
    $db = Database::getInstance();
    $id_item_os = intval($id_item_os);
    $nova_quantidade = intval($nova_quantidade);

    $sql_item = "SELECT * FROM item_os WHERE id_item_os = $id_item_os";
    $result = $db->query($sql_item);
    $item = $result->fetch_assoc();

    if (!$item) {
        return ['sucesso' => false, 'mensagem' => 'Item não encontrado'];
    }

    $id_os = intval($item['id_os']);
    $quant_antiga = intval($item['quantidade']);

    if (!empty($item['id_peca'])) {
        $id_peca = intval($item['id_peca']);
        $diff = $nova_quantidade - $quant_antiga;

        if ($diff > 0) {
            $sql_estoque = "SELECT quantidade FROM estoque WHERE id_peca = $id_peca";
            $res = $db->query($sql_estoque);
            $estoque = $res->fetch_assoc();
            if (intval($estoque['quantidade']) < $diff) {
                return ['sucesso' => false, 'mensagem' => 'Estoque insuficiente para aumentar quantidade'];
            }
            $sql_atualiza = "UPDATE estoque SET quantidade = quantidade - $diff WHERE id_peca = $id_peca";
            $db->query($sql_atualiza);
        } elseif ($diff < 0) {
            $devolve = abs($diff);
            $sql_atualiza = "UPDATE estoque SET quantidade = quantidade + $devolve WHERE id_peca = $id_peca";
            $db->query($sql_atualiza);
        }
    }

    $valor_unitario = floatval($item['valor_unitario']);
    $novo_subtotal = $nova_quantidade * $valor_unitario;
    $sql_update = "UPDATE item_os SET quantidade = $nova_quantidade, subtotal = $novo_subtotal WHERE id_item_os = $id_item_os";
    if ($db->query($sql_update)) {
        atualizar_valor_total_os($id_os);
        return ['sucesso' => true, 'mensagem' => 'Item atualizado com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao atualizar item'];
    }
}

function remover_item_os($id_item_os)
{
    $db = Database::getInstance();
    $id_item_os = intval($id_item_os);

    $sql_item = "SELECT * FROM item_os WHERE id_item_os = $id_item_os";
    $result = $db->query($sql_item);
    $item = $result->fetch_assoc();

    if (!$item) {
        return ['sucesso' => false, 'mensagem' => 'Item não encontrado'];
    }

    $id_os = intval($item['id_os']);

    if (!empty($item['id_peca'])) {
        $id_peca = intval($item['id_peca']);
        $quant = intval($item['quantidade']);
        $sql_atualiza = "UPDATE estoque SET quantidade = quantidade + $quant WHERE id_peca = $id_peca";
        $db->query($sql_atualiza);
    }

    $sql_del = "DELETE FROM item_os WHERE id_item_os = $id_item_os";
    if ($db->query($sql_del)) {
        atualizar_valor_total_os($id_os);
        return ['sucesso' => true, 'mensagem' => 'Item removido com sucesso'];
    } else {
        return ['sucesso' => false, 'mensagem' => 'Erro ao remover item'];
    }
}

function atualizar_valor_total_os($id_os)
{
    $db = Database::getInstance();
    $id_os = intval($id_os);

    $sql_total = "SELECT COALESCE(SUM(subtotal), 0) as total FROM item_os WHERE id_os = $id_os";
    $result = $db->query($sql_total);
    $dados = $result->fetch_assoc();
    $total = $dados['total'];

    $sql = "UPDATE ordem_servico SET valor_total = $total WHERE id_os = $id_os";
    $db->query($sql);
}




function obter_pecas()
{
    $db = Database::getInstance();
    $sql = "SELECT p.*, e.quantidade, e.quantidade_minima FROM peca p 
            LEFT JOIN estoque e ON p.id_peca = e.id_peca 
            ORDER BY p.nome ASC";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function criar_peca($nome, $descricao, $valor_unitario, $quantidade = 0, $quantidade_minima = 10, $local_armazenamento = null)
{
    $db = Database::getInstance();
    $nome = $db->escape($nome);
    $descricao = $db->escape($descricao);
    $valor_unitario = floatval($valor_unitario);
    $quantidade = intval($quantidade);
    $quantidade_minima = intval($quantidade_minima);
    $local_armazenamento = $local_armazenamento ? $db->escape($local_armazenamento) : null;

    $sql = "INSERT INTO peca (nome, descricao, valor_unitario) VALUES ('$nome', '$descricao', $valor_unitario)";
    if (!$db->query($sql)) {
        return ['sucesso' => false, 'mensagem' => 'Erro ao inserir peça: ' . $db->error()];
    }

    $id_peca = $db->lastInsertId();

    $local_sql = $local_armazenamento ? "'$local_armazenamento'" : "NULL";
    $sql_estoque = "INSERT INTO estoque (id_peca, quantidade, quantidade_minima, local_armazenamento) VALUES ($id_peca, $quantidade, $quantidade_minima, $local_sql)";
    if (!$db->query($sql_estoque)) {

        $db->query("DELETE FROM peca WHERE id_peca = $id_peca");
        return ['sucesso' => false, 'mensagem' => 'Erro ao inserir estoque inicial: ' . $db->error()];
    }

    return ['sucesso' => true, 'mensagem' => 'Peça e estoque adicionados com sucesso', 'id_peca' => $id_peca];
}

function obter_pecas_estoque_baixo()
{
    $db = Database::getInstance();
    $sql = "SELECT * FROM vw_pecas_estoque_baixo ORDER BY quantidade ASC";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function editar_peca($id_peca, $nome, $descricao, $valor_unitario, $quantidade, $quantidade_minima, $local_armazenamento)
{
    $db = Database::getInstance();
    $id_peca = intval($id_peca);
    $nome = $db->escape($nome);
    $descricao = $db->escape($descricao);
    $valor_unitario = floatval($valor_unitario);
    $quantidade = intval($quantidade);
    $quantidade_minima = intval($quantidade_minima);
    $local_armazenamento = $local_armazenamento ? $db->escape($local_armazenamento) : null;

    $sql = "UPDATE peca SET nome = '$nome', descricao = '$descricao', valor_unitario = $valor_unitario WHERE id_peca = $id_peca";
    if (!$db->query($sql)) {
        return ['sucesso' => false, 'mensagem' => 'Erro ao editar peça: ' . $db->error()];
    }

    $local_sql = $local_armazenamento ? "'$local_armazenamento'" : "NULL";
    $sql_estoque = "UPDATE estoque SET quantidade = $quantidade, quantidade_minima = $quantidade_minima, local_armazenamento = $local_sql WHERE id_peca = $id_peca";
    if (!$db->query($sql_estoque)) {
        return ['sucesso' => false, 'mensagem' => 'Erro ao atualizar estoque: ' . $db->error()];
    }

    return ['sucesso' => true, 'mensagem' => 'Peça atualizada com sucesso'];
}

function remover_peca($id_peca)
{
    $db = Database::getInstance();
    $id_peca = intval($id_peca);

    $sql_check = "SELECT COUNT(*) as total FROM item_os WHERE id_peca = $id_peca";
    $result = $db->query($sql_check);
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        return ['sucesso' => false, 'mensagem' => 'Não é possível remover peça que está em uso em ordens de serviço'];
    }

    $sql_estoque = "DELETE FROM estoque WHERE id_peca = $id_peca";
    if (!$db->query($sql_estoque)) {
        return ['sucesso' => false, 'mensagem' => 'Erro ao remover estoque: ' . $db->error()];
    }

    $sql = "DELETE FROM peca WHERE id_peca = $id_peca";
    if (!$db->query($sql)) {
        return ['sucesso' => false, 'mensagem' => 'Erro ao remover peça: ' . $db->error()];
    }

    return ['sucesso' => true, 'mensagem' => 'Peça removida com sucesso'];
}




function obter_resumo_executivo()
{
    $db = Database::getInstance();
    $sql = "SELECT * FROM vw_resumo_executivo";
    $result = $db->query($sql);
    return $result->fetch_assoc();
}

function obter_desempenho_funcionarios()
{
    $db = Database::getInstance();
    $sql = "SELECT * FROM vw_desempenho_funcionarios ORDER BY total_faturado DESC";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function obter_faturamento_periodo($data_inicio, $data_fim)
{
    $db = Database::getInstance();
    $data_inicio = $db->escape($data_inicio);
    $data_fim = $db->escape($data_fim);

    $sql = "SELECT DATE(os.data_abertura) AS data_servico,
                   COUNT(*) AS quantidade_os,
                   SUM(os.valor_total) AS total_faturado,
                   AVG(os.valor_total) AS valor_medio
            FROM ordem_servico os
            WHERE os.status = 'Concluida' AND DATE(os.data_abertura) BETWEEN '$data_inicio' AND '$data_fim'
            GROUP BY DATE(os.data_abertura)
            ORDER BY data_servico DESC";

    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}
