<?php


$mensagem = '';
$veiculos = obter_veiculos();
$servicos = [];
$pecas = obter_pecas();
$funcionarios = [];
$db = Database::getInstance();

$result = $db->query("SELECT * FROM servico ORDER BY nome ASC");
$servicos = $result->fetch_all(MYSQLI_ASSOC);

$result = $db->query("SELECT * FROM funcionario ORDER BY nome ASC");
$funcionarios = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'criar_os') {
        $resultado = criar_ordem_servico($_POST['id_veiculo'], $_POST['id_funcionario']);
        $mensagem = $resultado['mensagem'];
    } elseif ($acao === 'adicionar_servico') {
        $resultado = adicionar_item_os_servico($_POST['id_os'], $_POST['id_servico'], $_POST['quantidade']);
        $mensagem = $resultado['mensagem'];
    } elseif ($acao === 'adicionar_peca') {
        $resultado = adicionar_item_os_peca($_POST['id_os'], $_POST['id_peca'], $_POST['quantidade']);
        $mensagem = $resultado['mensagem'];
    } elseif ($acao === 'editar_item') {
        $resultado = editar_item_os_quantidade($_POST['id_item_os'], $_POST['quantidade']);
        $mensagem = $resultado['mensagem'];
    } elseif ($acao === 'remover_item') {
        $resultado = remover_item_os($_POST['id_item_os']);
        $mensagem = $resultado['mensagem'];
    } elseif ($acao === 'atualizar_status') {
        $resultado = atualizar_status_os($_POST['id_os'], $_POST['novo_status']);
        $mensagem = $resultado['mensagem'];
    } elseif ($acao === 'deletar_os') {
        $resultado = deletar_ordem_servico($_POST['id_os']);
        $mensagem = $resultado['mensagem'];
        if ($resultado['sucesso']) {

            header('Location: index.php?page=ordensservico');
            exit;
        }
    }
}

$ordens = obter_ordens_servico();
$os_detalhe = null;

if (isset($_GET['id'])) {
    $os_detalhe = obter_ordem_servico($_GET['id']);
}
?>

<div class="page-ordensservico">
    <h2>📋 Ordens de Serviço</h2>

    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo (strpos($mensagem, 'sucesso') !== false || strpos($mensagem, 'atualizado') !== false) ? 'success' : 'error'; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <?php if ($os_detalhe): ?>

        <div class="card">
            <h3>Ordem de Serviço #<?php echo $os_detalhe['id_os']; ?></h3>

            <div class="info-grid">
                <div>
                    <strong>Cliente:</strong> <?php echo $os_detalhe['cliente_nome']; ?>
                </div>
                <div>
                    <strong>Veículo:</strong> <?php echo $os_detalhe['veiculo_marca'] . ' ' . $os_detalhe['veiculo_modelo']; ?>
                </div>
                <div>
                    <strong>Placa:</strong> <?php echo $os_detalhe['veiculo_placa']; ?>
                </div>
                <div>
                    <strong>Status:</strong> <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $os_detalhe['status'])); ?>"><?php echo $os_detalhe['status']; ?></span>
                </div>
                <div>
                    <strong>Mecânico:</strong> <?php echo $os_detalhe['funcionario_nome'] ?? 'Não atribuído'; ?>
                </div>
                <div>
                    <strong>Valor Total:</strong> R$ <?php echo number_format($os_detalhe['valor_total'], 2, ',', '.'); ?>
                </div>
            </div>

            <hr>


            <div style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
                <form method="POST" class="form-inline">
                    <input type="hidden" name="acao" value="atualizar_status">
                    <input type="hidden" name="id_os" value="<?php echo $os_detalhe['id_os']; ?>">
                    <label>Novo Status:</label>
                    <select name="novo_status" required>
                        <option value="">Selecione...</option>
                        <option value="Aberta" <?php echo ($os_detalhe['status'] === 'Aberta') ? 'selected' : ''; ?>>Aberta</option>
                        <option value="Em Progresso" <?php echo ($os_detalhe['status'] === 'Em Progresso') ? 'selected' : ''; ?>>Em Progresso</option>
                        <option value="Concluida" <?php echo ($os_detalhe['status'] === 'Concluida') ? 'selected' : ''; ?>>Concluída</option>
                        <option value="Cancelada" <?php echo ($os_detalhe['status'] === 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                    </select>
                    <button type="submit" class="btn btn-primary">✓ Atualizar</button>
                </form>

                <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja deletar esta ordem de serviço? Esta ação não pode ser desfeita.');">
                    <input type="hidden" name="acao" value="deletar_os">
                    <input type="hidden" name="id_os" value="<?php echo $os_detalhe['id_os']; ?>">
                    <button type="submit" class="btn btn-danger">🗑️ Deletar OS</button>
                </form>
            </div>

            <hr>


            <h4>Itens da Ordem de Serviço</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Item</th>
                        <th>Quantidade</th>
                        <th>Valor Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $itens = obter_itens_os($os_detalhe['id_os']);
                    foreach ($itens as $item):
                    ?>
                        <tr>
                            <td><?php echo $item['tipo_item']; ?></td>
                            <td><?php echo $item['item_nome']; ?></td>
                            <td>
                                <form method="POST" class="form-inline" style="display:inline-block;">
                                    <input type="hidden" name="acao" value="editar_item">
                                    <input type="hidden" name="id_item_os" value="<?php echo $item['id_item_os']; ?>">
                                    <input type="number" name="quantidade" value="<?php echo $item['quantidade']; ?>" min="1" style="width:80px;">
                                    <button type="submit" class="btn btn-sm btn-primary">Editar</button>
                                </form>
                                <form method="POST" style="display:inline-block;margin-left:8px;">
                                    <input type="hidden" name="acao" value="remover_item">
                                    <input type="hidden" name="id_item_os" value="<?php echo $item['id_item_os']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remover este item?');">Remover</button>
                                </form>
                            </td>
                            <td>R$ <?php echo number_format($item['valor_unitario'], 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <hr>


            <h4>Adicionar Item</h4>


            <div class="card-collapsible">
                <div class="card-header">
                    <h5 style="margin: 0;">Adicionar Serviço</h5>
                    <button type="button" class="toggle-btn">+</button>
                </div>
                <div class="card-body collapsed">
                    <form method="POST" class="form">
                        <input type="hidden" name="acao" value="adicionar_servico">
                        <input type="hidden" name="id_os" value="<?php echo $os_detalhe['id_os']; ?>">

                        <div class="form-group">
                            <label>Serviço</label>
                            <select name="id_servico" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($servicos as $servico): ?>
                                    <option value="<?php echo $servico['id_servico']; ?>">
                                        <?php echo $servico['nome'] . ' - R$ ' . number_format($servico['valor_padrao'], 2, ',', '.'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantidade</label>
                            <input type="number" name="quantidade" value="1" min="1" required>
                        </div>

                        <button type="submit" class="btn btn-primary">✓ Adicionar Serviço</button>
                    </form>
                </div>
            </div>


            <div class="card-collapsible">
                <div class="card-header">
                    <h5 style="margin: 0;">Adicionar Peça</h5>
                    <button type="button" class="toggle-btn">+</button>
                </div>
                <div class="card-body collapsed">
                    <form method="POST" class="form">
                        <input type="hidden" name="acao" value="adicionar_peca">
                        <input type="hidden" name="id_os" value="<?php echo $os_detalhe['id_os']; ?>">

                        <div class="form-group">
                            <label>Peça</label>
                            <select name="id_peca" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($pecas as $peca): ?>
                                    <option value="<?php echo $peca['id_peca']; ?>" <?php echo $peca['quantidade'] < 1 ? 'disabled' : ''; ?>>
                                        <?php echo $peca['nome'] . ' - Estoque: ' . $peca['quantidade']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantidade</label>
                            <input type="number" name="quantidade" value="1" min="1" required>
                        </div>

                        <button type="submit" class="btn btn-primary">✓ Adicionar Peça</button>
                    </form>
                </div>
            </div>

            <hr>
            <a href="index.php?page=ordensservico" class="btn btn-secondary">← Voltar</a>
        </div>

    <?php else: ?>

        <div class="card-collapsible">
            <div class="card-header">
                <h3>Nova Ordem de Serviço</h3>
                <button type="button" class="toggle-btn">+</button>
            </div>
            <div class="card-body collapsed">
                <form method="POST" class="form">
                    <input type="hidden" name="acao" value="criar_os">

                    <div class="form-group">
                        <label>Veículo</label>
                        <select name="id_veiculo" required>
                            <option value="">Selecione um veículo</option>
                            <?php foreach ($veiculos as $veiculo): ?>
                                <option value="<?php echo $veiculo['id_veiculo']; ?>">
                                    <?php echo $veiculo['cliente_nome'] . ' - ' . $veiculo['marca'] . ' ' . $veiculo['modelo'] . ' (' . $veiculo['placa'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Mecânico (opcional)</label>
                        <select name="id_funcionario">
                            <option value="">Sem atribuição</option>
                            <?php foreach ($funcionarios as $func): ?>
                                <option value="<?php echo $func['id_funcionario']; ?>">
                                    <?php echo $func['nome'] . ' (' . $func['especialidade'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">✓ Criar Ordem de Serviço</button>
                </form>
            </div>
        </div>

        <hr>


        <div class="section">
            <h3>Ordens de Serviço</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Veículo</th>
                        <th>Status</th>
                        <th>Mecânico</th>
                        <th>Abertura</th>
                        <th>Valor</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordens as $os): ?>
                        <tr>
                            <td>#<?php echo $os['id_os']; ?></td>
                            <td><?php echo $os['cliente_nome']; ?></td>
                            <td><?php echo $os['veiculo_marca'] . ' ' . $os['veiculo_modelo']; ?></td>
                            <td><span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $os['status'])); ?>"><?php echo $os['status']; ?></span></td>
                            <td><?php echo $os['funcionario_nome'] ?? '-'; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($os['data_abertura'])); ?></td>
                            <td>R$ <?php echo number_format($os['valor_total'], 2, ',', '.'); ?></td>
                            <td><a href="index.php?page=ordensservico&id=<?php echo $os['id_os']; ?>" class="btn btn-sm btn-info">Detalhes</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>