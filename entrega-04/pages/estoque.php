<?php


$pecas = obter_pecas();
$pecas_baixo = obter_pecas_estoque_baixo();

$mensagem = '';
$tipo_mensagem = 'success';
$editando_peca = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'criar_peca') {
        $nome = $_POST['nome'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $valor = $_POST['valor_unitario'] ?? 0;
        $quant = $_POST['quantidade'] ?? 0;
        $quant_min = $_POST['quantidade_minima'] ?? 10;
        $local = $_POST['local_armazenamento'] ?? null;

        $res = criar_peca($nome, $descricao, $valor, $quant, $quant_min, $local);
        $mensagem = $res['mensagem'];
        $tipo_mensagem = $res['sucesso'] ? 'success' : 'error';
    } elseif ($acao === 'editar_peca') {
        $id_peca = $_POST['id_peca'] ?? 0;
        $nome = $_POST['nome'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $valor = $_POST['valor_unitario'] ?? 0;
        $quant = $_POST['quantidade'] ?? 0;
        $quant_min = $_POST['quantidade_minima'] ?? 10;
        $local = $_POST['local_armazenamento'] ?? null;

        $res = editar_peca($id_peca, $nome, $descricao, $valor, $quant, $quant_min, $local);
        $mensagem = $res['mensagem'];
        $tipo_mensagem = $res['sucesso'] ? 'success' : 'error';
    } elseif ($acao === 'remover_peca') {
        $id_peca = $_POST['id_peca'] ?? 0;
        $res = remover_peca($id_peca);
        $mensagem = $res['mensagem'];
        $tipo_mensagem = $res['sucesso'] ? 'success' : 'error';
    }
}

$id_editar = $_GET['editar'] ?? null;
if ($id_editar) {
    $pecas_temp = obter_pecas();
    foreach ($pecas_temp as $p) {
        if ($p['id_peca'] == $id_editar) {
            $editando_peca = $p;
            break;
        }
    }
}

$pecas = obter_pecas();
$pecas_baixo = obter_pecas_estoque_baixo();
?>

<div class="page-estoque">
    <h2>📦 Gestão de Estoque</h2>


    <div class="cards-grid">
        <div class="card card-info">
            <h3>📦 Total de Peças</h3>
            <p class="valor"><?php echo count($pecas); ?></p>
        </div>

        <div class="card card-warning">
            <h3>⚠️ Estoque Baixo</h3>
            <p class="valor"><?php echo count($pecas_baixo); ?></p>
        </div>

        <div class="card card-danger">
            <h3>🚨 Crítico (Zero)</h3>
            <p class="valor">
                <?php
                $criticas = array_filter($pecas_baixo, function ($p) {
                    return $p['quantidade'] == 0;
                });
                echo count($criticas);
                ?>
            </p>
        </div>
    </div>

    <hr>

    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>


    <div class="card-collapsible">
        <div class="card-header">
            <h3><?php echo $editando_peca ? '✎ Editar Peça' : '➕ Adicionar Peça / Produto'; ?></h3>
            <button type="button" class="toggle-btn"><?php echo $editando_peca ? '−' : '+'; ?></button>
        </div>
        <div class="card-body <?php echo $editando_peca ? '' : 'collapsed'; ?>">
            <form method="POST" class="form">
                <input type="hidden" name="acao" value="<?php echo $editando_peca ? 'editar_peca' : 'criar_peca'; ?>">
                <?php if ($editando_peca): ?>
                    <input type="hidden" name="id_peca" value="<?php echo $editando_peca['id_peca']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" required placeholder="Nome da peça" value="<?php echo $editando_peca ? $editando_peca['nome'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Descrição</label>
                    <input type="text" name="descricao" placeholder="Descrição" value="<?php echo $editando_peca ? ($editando_peca['descricao'] ?? '') : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Valor Unitário</label>
                    <input type="number" step="0.01" name="valor_unitario" required placeholder="0.00" value="<?php echo $editando_peca ? $editando_peca['valor_unitario'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Quantidade</label>
                    <input type="number" name="quantidade" value="<?php echo $editando_peca ? $editando_peca['quantidade'] : '0'; ?>" min="0" placeholder="0">
                </div>

                <div class="form-group">
                    <label>Quantidade Mínima</label>
                    <input type="number" name="quantidade_minima" value="<?php echo $editando_peca ? $editando_peca['quantidade_minima'] : '10'; ?>" min="0" placeholder="10">
                </div>

                <div class="form-group">
                    <label>Local de Armazenamento</label>
                    <input type="text" name="local_armazenamento" placeholder="Ex: Prateleira A" value="<?php echo $editando_peca ? ($editando_peca['local_armazenamento'] ?? '') : ''; ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editando_peca ? '✓ Atualizar Peça' : '✓ Adicionar Peça'; ?>
                    </button>
                    <?php if ($editando_peca): ?>
                        <a href="index.php?page=estoque" class="btn btn-secondary">✕ Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>


    <div class="section">
        <h3>⚠️ Peças com Estoque Baixo</h3>
        <?php if (count($pecas_baixo) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Peça</th>
                        <th>Quantidade</th>
                        <th>Mínima</th>
                        <th>Falta Repor</th>
                        <th>Custo Reposição</th>
                        <th>Local</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pecas_baixo as $peca): ?>
                        <tr class="<?php echo $peca['quantidade'] == 0 ? 'row-critical' : ''; ?>">
                            <td><?php echo $peca['peca_nome']; ?></td>
                            <td><?php echo $peca['quantidade']; ?></td>
                            <td><?php echo $peca['quantidade_minima']; ?></td>
                            <td><?php echo $peca['falta_repor']; ?></td>
                            <td>R$ <?php echo number_format($peca['falta_repor'] * $peca['valor_unitario'], 2, ',', '.'); ?></td>
                            <td><?php echo $peca['local_armazenamento']; ?></td>
                            <td>
                                <span class="badge <?php echo $peca['quantidade'] == 0 ? 'badge-danger' : 'badge-warning'; ?>">
                                    <?php echo $peca['status_estoque']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-success">✓ Todos os produtos estão com estoque adequado</p>
        <?php endif; ?>
    </div>

    <hr>


    <div class="section" style="margin-bottom: 60px;">
        <h3>Todas as Peças</h3>
        <?php if (count($pecas) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Peça</th>
                        <th>Descrição</th>
                        <th>Quantidade</th>
                        <th>Mínima</th>
                        <th>Valor Unitário</th>
                        <th>Local</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pecas as $peca): ?>
                        <tr class="<?php echo $peca['quantidade'] <= $peca['quantidade_minima'] ? 'row-highlight' : ''; ?>">
                            <td><?php echo $peca['nome']; ?></td>
                            <td><?php echo $peca['descricao'] ?? '-'; ?></td>
                            <td><?php echo $peca['quantidade']; ?></td>
                            <td><?php echo $peca['quantidade_minima']; ?></td>
                            <td>R$ <?php echo number_format($peca['valor_unitario'], 2, ',', '.'); ?></td>
                            <td><?php echo $peca['local_armazenamento'] ?? '-'; ?></td>
                            <td>
                                <?php
                                if ($peca['quantidade'] == 0) {
                                    echo '<span class="badge badge-danger">CRÍTICO</span>';
                                } elseif ($peca['quantidade'] < $peca['quantidade_minima']) {
                                    echo '<span class="badge badge-warning">BAIXO</span>';
                                } else {
                                    echo '<span class="badge badge-success">OK</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="index.php?page=estoque&editar=<?php echo $peca['id_peca']; ?>" class="btn btn-sm btn-primary">✎ Editar</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja remover essa peça?');">
                                    <input type="hidden" name="acao" value="remover_peca">
                                    <input type="hidden" name="id_peca" value="<?php echo $peca['id_peca']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-info">ℹ️ Nenhuma peça cadastrada</p>
        <?php endif; ?>
    </div>
</div>