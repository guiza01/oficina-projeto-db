<?php


require_once 'php/config.php';
require_once 'php/functions.php';

$pagina = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - v<?php echo APP_VERSION; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">

        <aside class="sidebar">
            <div class="sidebar-header">
                <h1><?php echo APP_NAME; ?></h1>
                <p class="version">v<?php echo APP_VERSION; ?></p>
            </div>

            <nav class="menu">
                <a href="index.php?page=dashboard" class="menu-item <?php echo $pagina === 'dashboard' ? 'ativo' : ''; ?>">
                    📊 Dashboard
                </a>
                <a href="index.php?page=clientes" class="menu-item <?php echo $pagina === 'clientes' ? 'ativo' : ''; ?>">
                    👥 Clientes
                </a>
                <a href="index.php?page=veiculos" class="menu-item <?php echo $pagina === 'veiculos' ? 'ativo' : ''; ?>">
                    🚗 Veículos
                </a>
                <a href="index.php?page=ordensservico" class="menu-item <?php echo $pagina === 'ordensservico' ? 'ativo' : ''; ?>">
                    📋 Ordens de Serviço
                </a>
                <a href="index.php?page=estoque" class="menu-item <?php echo $pagina === 'estoque' ? 'ativo' : ''; ?>">
                    📦 Estoque
                </a>
                <a href="index.php?page=relatorios" class="menu-item <?php echo $pagina === 'relatorios' ? 'ativo' : ''; ?>">
                    📈 Relatórios
                </a>
                <a href="index.php?page=funcionarios" class="menu-item <?php echo $pagina === 'funcionarios' ? 'ativo' : ''; ?>">
                    👷 Funcionários
                </a>
            </nav>
        </aside>


        <main class="content">

            <header class="header">
                <h1 id="page-title">Dashboard</h1>
                <div class="header-info">
                    <span><?php echo date('d/m/Y H:i'); ?></span>
                </div>
            </header>


            <div class="main-area">
                <?php

                switch ($pagina) {
                    case 'clientes':
                        include 'pages/clientes.php';
                        break;
                    case 'veiculos':
                        include 'pages/veiculos.php';
                        break;
                    case 'ordensservico':
                        include 'pages/ordensservico.php';
                        break;
                    case 'estoque':
                        include 'pages/estoque.php';
                        break;
                    case 'relatorios':
                        include 'pages/relatorios.php';
                        break;
                    case 'funcionarios':
                        include 'pages/funcionarios.php';
                        break;
                    default:
                        include 'pages/dashboard.php';
                }
                ?>
            </div>
        </main>
    </div>


    <footer class="footer">
        <p>&copy; 2026 Sistema de Gestão - Oficina Mecânica. Todos os direitos reservados.</p>
    </footer>

    <script src="js/main.js"></script>
</body>

</html>