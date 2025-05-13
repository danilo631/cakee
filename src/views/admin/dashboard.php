<?php
session_start();
require_once '../../src/config/database.php';
require_once '../../src/models/Produto.php';
require_once '../../src/models/Usuario.php';

// Verificar se o usuário está logado e é vendedor
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'vendedor') {
    header('Location: ../login.php');
    exit;
}

$totalProdutos = count(Produto::listarTodos());
$totalClientes = count(Usuario::listarPorTipo('cliente'));

include '../../src/views/templates/admin-header.php';
?>

<main class="admin-dashboard">
    <h1>Painel Administrativo</h1>
    
    <div class="admin-cards">
        <div class="admin-card">
            <h2>Total de Produtos</h2>
            <p><?= $totalProdutos ?></p>
            <a href="produtos/listar.php" class="btn-admin">Gerenciar</a>
        </div>
        
        <div class="admin-card">
            <h2>Total de Clientes</h2>
            <p><?= $totalClientes ?></p>
            <a href="clientes/" class="btn-admin">Ver Todos</a>
        </div>
    </div>
    
    <div class="admin-actions">
        <a href="produtos/adicionar.php" class="btn-primary">
            <i class="fas fa-plus"></i> Adicionar Novo Produto
        </a>
    </div>
</main>

<?php include '../../src/views/templates/admin-footer.php'; ?>