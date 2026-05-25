<?php


$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'criar_funcionario') {
        $res = criar_funcionario($_POST['nome'], $_POST['cpf'], $_POST['especialidade'] ?? null, $_POST['telefone'] ?? null, $_POST['salario'] ?? 0, $_POST['data_admissao'] ?? null);
        $mensagem = $res['mensagem'];
        $tipo_mensagem = $res['sucesso'] ? 'success' : 'error';
    } elseif ($acao === 'atualizar_funcionario') {
        $res = atualizar_funcionario($_POST['id_funcionario'], $_POST['nome'], $_POST['cpf'], $_POST['especialidade'] ?? null, $_POST['telefone'] ?? null, $_POST['salario'] ?? 0, $_POST['data_admissao'] ?? null);
        $mensagem = $res['mensagem'];
        $tipo_mensagem = $res['sucesso'] ? 'success' : 'error';
    } elseif ($acao === 'deletar_funcionario') {
        $res = deletar_funcionario($_POST['id_funcionario']);
        $mensagem = $res['mensagem'];
        $tipo_mensagem = $res['sucesso'] ? 'success' : 'error';
    }
}

$funcionarios = obter_funcionarios();
$editar = null;

if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    foreach ($funcionarios as $f) {
        if ($f['id_funcionario'] == $id) {
            $editar = $f;
            break;
        }
    }
}
?>

<div class="page-funcionarios">
    <h2>👷 Controle de Funcionários</h2>

    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>


    <div class="card-collapsible">
        <div class="card-header">
            <h3><?php echo $editar ? 'Editar Funcionário' : 'Novo Funcionário'; ?></h3>
            <button type="button" class="toggle-btn"><?php echo $editar ? '−' : '+'; ?></button>
        </div>
        <div class="card-body <?php echo $editar ? '' : 'collapsed'; ?>">
            <form method="POST" class="form">
                <?php if ($editar): ?>
                    <input type="hidden" name="acao" value="atualizar_funcionario">
                    <input type="hidden" name="id_funcionario" value="<?php echo $editar['id_funcionario']; ?>">
                <?php else: ?>
                    <input type="hidden" name="acao" value="criar_funcionario">
                <?php endif; ?>

                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" placeholder="Nome completo" required value="<?php echo $editar['nome'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" name="cpf" placeholder="00000000000" required maxlength="11" value="<?php echo $editar['cpf'] ?? ''; ?>" <?php echo $editar ? 'readonly' : ''; ?>>
                </div>

                <div class="form-group">
                    <label>Especialidade</label>
                    <input type="text" name="especialidade" placeholder="Ex: Motor, Suspensão..." value="<?php echo $editar['especialidade'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" placeholder="(87) 98888-8888" value="<?php echo $editar['telefone'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Salário</label>
                    <input type="number" step="0.01" placeholder="0.00" name="salario" value="<?php echo $editar['salario'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Data Admissão</label>
                    <input type="date" name="data_admissao" value="<?php echo isset($editar['data_admissao']) ? date('Y-m-d', strtotime($editar['data_admissao'])) : ''; ?>">
                </div>

                <button type="submit" class="btn btn-primary">
                    <?php echo $editar ? '✓ Atualizar' : '✓ Adicionar'; ?>
                </button>
                <?php if ($editar): ?>
                    <a href="index.php?page=funcionarios" class="btn btn-secondary">✕ Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <hr>


    <div class="section" style="margin-bottom: 60px;">
        <h3>Lista de Funcionários</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Especialidade</th>
                    <th>Telefone</th>
                    <th>Salário</th>
                    <th>Data Admissão</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($funcionarios as $f): ?>
                    <tr>
                        <td>#<?php echo $f['id_funcionario']; ?></td>
                        <td><?php echo $f['nome']; ?></td>
                        <td><?php echo $f['cpf']; ?></td>
                        <td><?php echo $f['especialidade'] ?? '-'; ?></td>
                        <td><?php echo $f['telefone'] ?? '-'; ?></td>
                        <td>R$ <?php echo number_format($f['salario'], 2, ',', '.'); ?></td>
                        <td><?php echo $f['data_admissao'] ? date('d/m/Y', strtotime($f['data_admissao'])) : '-'; ?></td>
                        <td>
                            <a href="index.php?page=funcionarios&editar=<?php echo $f['id_funcionario']; ?>" class="btn btn-sm btn-primary">⌨ Editar</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza?');">
                                <input type="hidden" name="acao" value="deletar_funcionario">
                                <input type="hidden" name="id_funcionario" value="<?php echo $f['id_funcionario']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">🗑️ Remover</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (count($funcionarios) === 0): ?>
            <p class="alert alert-info">Nenhum funcionário cadastrado</p>
        <?php endif; ?>
    </div>
</div>