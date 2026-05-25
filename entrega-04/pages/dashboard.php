<?php


$resumo = obter_resumo_executivo();
$ordens_recentes = obter_ordens_servico();
$pecas_baixo = obter_pecas_estoque_baixo();
?>

<div class="dashboard">
    <h2>Dashboard da Oficina</h2>


    <div class="cards-grid">
        <div class="card card-primary">
            <h3>👥 Clientes</h3>
            <p class="valor"><?php echo $resumo['total_clientes']; ?></p>
            <small>Total cadastrado</small>
        </div>

        <div class="card card-info">
            <h3>🚗 Veículos</h3>
            <p class="valor"><?php echo $resumo['total_veiculos']; ?></p>
            <small>Total na base</small>
        </div>

        <div class="card card-warning">
            <h3>📋 Ordens de Serviço</h3>
            <p class="valor"><?php echo $resumo['total_ordens']; ?></p>
            <small>Total geral</small>
        </div>

        <div class="card card-success">
            <h3>✅ Concluídas</h3>
            <p class="valor"><?php echo $resumo['ordens_concluidas']; ?></p>
            <small>Hoje/Período</small>
        </div>

        <div class="card card-danger">
            <h3>⚠️ Estoque Baixo</h3>
            <p class="valor"><?php echo $resumo['pecas_estoque_baixo']; ?></p>
            <small>Peças para reabastecer</small>
        </div>

        <div class="card card-dark">
            <h3>💰 Faturamento</h3>
            <p class="valor">R$ <?php echo number_format($resumo['total_faturado'], 2, ',', '.'); ?></p>
            <small>Total concluído</small>
        </div>
    </div>

    <hr>


    <div class="section">
        <h3>📋 Ordens de Serviço em Progresso</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID OS</th>
                    <th>Cliente</th>
                    <th>Veículo</th>
                    <th>Status</th>
                    <th>Mecânico</th>
                    <th>Valor</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($ordens_recentes as $os) {
                    if ($os['status'] === 'Em Progresso') {
                        echo "<tr>";
                        echo "<td>#" . $os['id_os'] . "</td>";
                        echo "<td>" . $os['cliente_nome'] . "</td>";
                        echo "<td>" . $os['veiculo_marca'] . " " . $os['veiculo_modelo'] . "</td>";
                        echo "<td><span class='badge badge-warning'>" . $os['status'] . "</span></td>";
                        echo "<td>" . ($os['funcionario_nome'] ?? 'Não atribuído') . "</td>";
                        echo "<td>R$ " . number_format($os['valor_total'], 2, ',', '.') . "</td>";
                        echo "<td><a href='index.php?page=ordensservico&id=" . $os['id_os'] . "' class='btn btn-sm btn-info'>Ver</a></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <hr>


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
                        <th>Local</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pecas_baixo as $peca): ?>
                        <tr>
                            <td><?php echo $peca['peca_nome']; ?></td>
                            <td><?php echo $peca['quantidade']; ?></td>
                            <td><?php echo $peca['quantidade_minima']; ?></td>
                            <td><?php echo $peca['falta_repor']; ?></td>
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
</div>