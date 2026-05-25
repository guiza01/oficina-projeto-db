<?php


$mensagem = '';
$tipo_mensagem = 'success';
$veiculos = obter_veiculos();
$clientes = obter_clientes();
$editando_veiculo = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'criar_veiculo') {
        $resultado = criar_veiculo(
            $_POST['cpf_cliente'],
            $_POST['placa'],
            $_POST['marca'],
            $_POST['modelo'],
            $_POST['ano'],
            $_POST['cor']
        );
        $mensagem = $resultado['mensagem'];
        $tipo_mensagem = $resultado['sucesso'] ? 'success' : 'error';
    } elseif ($acao === 'editar_veiculo') {
        $resultado = editar_veiculo(
            $_POST['id_veiculo'],
            $_POST['cpf_cliente'],
            $_POST['placa'],
            $_POST['marca'],
            $_POST['modelo'],
            $_POST['ano'],
            $_POST['cor']
        );
        $mensagem = $resultado['mensagem'];
        $tipo_mensagem = $resultado['sucesso'] ? 'success' : 'error';
    } elseif ($acao === 'remover_veiculo') {
        $resultado = remover_veiculo($_POST['id_veiculo']);
        $mensagem = $resultado['mensagem'];
        $tipo_mensagem = $resultado['sucesso'] ? 'success' : 'error';
    }
}

$id_editar = $_GET['editar'] ?? null;
if ($id_editar) {
    $veiculo_temp = obter_veiculo($id_editar);
    if ($veiculo_temp) {
        $editando_veiculo = $veiculo_temp;
    }
}

$veiculos = obter_veiculos();
?>

<div class="page-veiculos">
    <h2>🚗 Gestão de Veículos</h2>

    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>


    <div class="card-collapsible">
        <div class="card-header">
            <h3><?php echo $editando_veiculo ? '✎ Editar Veículo' : '➕ Novo Veículo'; ?></h3>
            <button type="button" class="toggle-btn"><?php echo $editando_veiculo ? '−' : '+'; ?></button>
        </div>
        <div class="card-body <?php echo $editando_veiculo ? '' : 'collapsed'; ?>">
            <form method="POST" class="form">
                <input type="hidden" name="acao" value="<?php echo $editando_veiculo ? 'editar_veiculo' : 'criar_veiculo'; ?>">
                <?php if ($editando_veiculo): ?>
                    <input type="hidden" name="id_veiculo" value="<?php echo $editando_veiculo['id_veiculo']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Cliente</label>
                    <select name="cpf_cliente" required>
                        <option value="">Selecione um cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['cpf']; ?>" <?php echo ($editando_veiculo && $editando_veiculo['cpf_cliente'] === $cliente['cpf']) ? 'selected' : ''; ?>>
                                <?php echo $cliente['nome']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Placa</label>
                    <input type="text" name="placa" placeholder="ABC1234" required maxlength="10" value="<?php echo $editando_veiculo ? $editando_veiculo['placa'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Marca</label>
                    <input type="text" name="marca" placeholder="Fiat" required value="<?php echo $editando_veiculo ? $editando_veiculo['marca'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Modelo</label>
                    <input type="text" name="modelo" placeholder="Uno" required value="<?php echo $editando_veiculo ? $editando_veiculo['modelo'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Ano</label>
                    <input type="number" name="ano" placeholder="2020" required min="1900" value="<?php echo $editando_veiculo ? $editando_veiculo['ano'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Cor</label>
                    <input type="text" name="cor" placeholder="Branco" value="<?php echo $editando_veiculo ? $editando_veiculo['cor'] : ''; ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editando_veiculo ? '✓ Atualizar Veículo' : '✓ Adicionar Veículo'; ?>
                    </button>
                    <?php if ($editando_veiculo): ?>
                        <a href="index.php?page=veiculos" class="btn btn-secondary">✕ Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <hr>


    <div class="section" style="margin-bottom: 60px;">
        <h3>Lista de Veículos</h3>
        <?php if (count($veiculos) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Ano</th>
                        <th>Cor</th>
                        <th>Cliente</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($veiculos as $veiculo): ?>
                        <tr>
                            <td><?php echo $veiculo['placa']; ?></td>
                            <td><?php echo $veiculo['marca']; ?></td>
                            <td><?php echo $veiculo['modelo']; ?></td>
                            <td><?php echo $veiculo['ano']; ?></td>
                            <td><?php echo $veiculo['cor']; ?></td>
                            <td><?php echo $veiculo['cliente_nome']; ?></td>
                            <td>
                                <a href="index.php?page=veiculos&editar=<?php echo $veiculo['id_veiculo']; ?>" class="btn btn-sm btn-primary">✎ Editar</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja remover esse veículo?');">
                                    <input type="hidden" name="acao" value="remover_veiculo">
                                    <input type="hidden" name="id_veiculo" value="<?php echo $veiculo['id_veiculo']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-info">ℹ️ Nenhum veículo cadastrado</p>
        <?php endif; ?>
    </div>
</div>