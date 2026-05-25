<?php

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'criar') {
        $resultado = criar_cliente($_POST['cpf'], $_POST['nome'], $_POST['email'], $_POST['telefone'], $_POST['endereco']);
        $mensagem = $resultado['mensagem'];
        $tipo_mensagem = $resultado['sucesso'] ? 'success' : 'error';
    } elseif ($acao === 'atualizar') {
        $resultado = atualizar_cliente($_POST['cpf'], $_POST['nome'], $_POST['email'], $_POST['telefone'], $_POST['endereco']);
        $mensagem = $resultado['mensagem'];
        $tipo_mensagem = $resultado['sucesso'] ? 'success' : 'error';
    } elseif ($acao === 'deletar') {
        $resultado = deletar_cliente($_POST['cpf']);
        $mensagem = $resultado['mensagem'];
        $tipo_mensagem = $resultado['sucesso'] ? 'success' : 'error';
    }
}

$clientes = obter_clientes();
$cliente_edit = null;

if (isset($_GET['edit'])) {
    $cliente_edit = obter_cliente_por_cpf($_GET['edit']);
}
?>

<div class="page-clientes">
    <h2>👥 Gestão de Clientes</h2>

    <?php if ($mensagem): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>


    <div class="card-collapsible">
        <div class="card-header">
            <h3><?php echo $cliente_edit ? 'Editar Cliente' : 'Novo Cliente'; ?></h3>
            <button type="button" class="toggle-btn"><?php echo $cliente_edit ? '−' : '+'; ?></button>
        </div>
        <div class="card-body <?php echo $cliente_edit ? '' : 'collapsed'; ?>">
            <form method="POST" class="form">
                <input type="hidden" name="acao" value="<?php echo $cliente_edit ? 'atualizar' : 'criar'; ?>">

                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" name="cpf" placeholder="000.000.000-00"
                        value="<?php echo $cliente_edit['cpf'] ?? ''; ?>"
                        <?php echo $cliente_edit ? 'readonly' : 'required'; ?> maxlength="14">
                </div>

                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" placeholder="Nome completo"
                        value="<?php echo $cliente_edit['nome'] ?? ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="email@exemplo.com"
                        value="<?php echo $cliente_edit['email'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" placeholder="(87) 98888-8888"
                        value="<?php echo $cliente_edit['telefone'] ?? ''; ?>" maxlength="15" required>
                </div>

                <div class="form-group">
                    <label>Endereço</label>
                    <input type="text" name="endereco" placeholder="Rua, número, bairro..."
                        value="<?php echo $cliente_edit['endereco'] ?? ''; ?>">
                </div>

                <button type="submit" class="btn btn-primary">
                    <?php echo $cliente_edit ? '✓ Atualizar' : '✓ Adicionar'; ?>
                </button>
                <?php if ($cliente_edit): ?>
                    <a href="index.php?page=clientes" class="btn btn-secondary">✕ Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <hr>


    <div class="section">
        <h3>Lista de Clientes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>CPF</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente['cpf']; ?></td>
                        <td><?php echo $cliente['nome']; ?></td>
                        <td><?php echo $cliente['email'] ?? '-'; ?></td>
                        <td><?php echo $cliente['telefone']; ?></td>
                        <td><?php echo $cliente['endereco'] ?? '-'; ?></td>
                        <td>
                            <a href="index.php?page=clientes&edit=<?php echo $cliente['cpf']; ?>" class="btn btn-sm btn-primary">⌨ Editar</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza?');">
                                <input type="hidden" name="acao" value="deletar">
                                <input type="hidden" name="cpf" value="<?php echo $cliente['cpf']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">🗑️ Deletar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (count($clientes) === 0): ?>
            <p class="alert alert-info">Nenhum cliente cadastrado</p>
        <?php endif; ?>
    </div>
</div>