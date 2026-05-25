<?php


$resumo = obter_resumo_executivo();
$desempenho_funcionarios = obter_desempenho_funcionarios();
$faturamento = [];

$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-d');

$faturamento = obter_faturamento_periodo($data_inicio, $data_fim);
?>

<div class="page-relatorios">
    <h2>📈 Relatórios</h2>


    <div class="section">
        <h3>📊 Resumo Executivo</h3>
        <div class="cards-grid">
            <div class="card card-primary">
                <h4>Total de Clientes</h4>
                <p class="valor"><?php echo $resumo['total_clientes']; ?></p>
            </div>

            <div class="card card-info">
                <h4>Total de Veículos</h4>
                <p class="valor"><?php echo $resumo['total_veiculos']; ?></p>
            </div>

            <div class="card card-warning">
                <h4>Total de Ordens</h4>
                <p class="valor"><?php echo $resumo['total_ordens']; ?></p>
            </div>

            <div class="card card-success">
                <h4>Ordens Concluídas</h4>
                <p class="valor"><?php echo $resumo['ordens_concluidas']; ?></p>
            </div>

            <div class="card card-danger">
                <h4>Em Progresso</h4>
                <p class="valor"><?php echo $resumo['ordens_em_progresso']; ?></p>
            </div>

            <div class="card card-dark">
                <h4>Total Faturado</h4>
                <p class="valor">R$ <?php echo number_format($resumo['total_faturado'], 2, ',', '.'); ?></p>
            </div>

            <div class="card card-warning">
                <h4>Estoque Baixo</h4>
                <p class="valor"><?php echo $resumo['pecas_estoque_baixo']; ?></p>
            </div>

            <div class="card card-danger">
                <h4>Sem Estoque</h4>
                <p class="valor"><?php echo $resumo['pecas_sem_estoque']; ?></p>
            </div>
        </div>
    </div>

    <hr>


    <div class="section">
        <h3>👨‍🔧 Desempenho de Funcionários</h3>
        <?php if (count($desempenho_funcionarios) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Funcionário</th>
                        <th>Especialidade</th>
                        <th>Ordens</th>
                        <th>Total Faturado</th>
                        <th>Valor Médio</th>
                        <th>Taxa Conclusão</th>
                        <th>Última Ordem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($desempenho_funcionarios as $func): ?>
                        <tr>
                            <td><?php echo $func['nome']; ?></td>
                            <td><?php echo $func['especialidade'] ?? '-'; ?></td>
                            <td><?php echo $func['total_ordens']; ?></td>
                            <td>R$ <?php echo number_format($func['total_faturado'], 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($func['valor_medio_por_os'], 2, ',', '.'); ?></td>
                            <td><?php echo $func['percentual_conclusao']; ?>%</td>
                            <td><?php echo $func['ultima_ordem'] ? date('d/m/Y', strtotime($func['ultima_ordem'])) : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-info">Sem registros</p>
        <?php endif; ?>
    </div>

    <hr>


    <div class="section">
        <h3>💰 Faturamento por Período</h3>

        <form method="GET" class="form-inline">
            <input type="hidden" name="page" value="relatorios">
            <label>Data Início:</label>
            <input type="date" name="data_inicio" value="<?php echo $data_inicio; ?>">
            <label>Data Fim:</label>
            <input type="date" name="data_fim" value="<?php echo $data_fim; ?>">
            <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
        </form>

        <hr>

        <?php if (count($faturamento) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Quantidade OS</th>
                        <th>Total Faturado</th>
                        <th>Valor Médio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_geral = 0;
                    foreach ($faturamento as $dia):
                        $total_geral += $dia['total_faturado'];
                    ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($dia['data_servico'])); ?></td>
                            <td><?php echo $dia['quantidade_os']; ?></td>
                            <td>R$ <?php echo number_format($dia['total_faturado'], 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($dia['valor_medio'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-total">
                        <th colspan="2">TOTAL DO PERÍODO</th>
                        <th>R$ <?php echo number_format($total_geral, 2, ',', '.'); ?></th>
                        <th></th>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-info">Nenhuma ordem concluída no período</p>
        <?php endif; ?>
    </div>
</div>