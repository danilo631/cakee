<?php
session_start();
require_once '../../../src/config/database.php';
require_once '../../../src/controllers/ProdutoController.php';

// Verificar se o usuário está logado e é vendedor
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'vendedor') {
    header('Location: ../../login.php');
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = ProdutoController::salvar($_POST, $_FILES['imagem']);
    
    if ($resultado['success']) {
        $_SESSION['mensagem'] = 'Produto cadastrado com sucesso!';
        header('Location: listar.php');
        exit;
    } else {
        $mensagem = $resultado['message'];
    }
}

include '../../../src/views/templates/admin-header.php';
?>

<main class="admin-form-page">
    <h1>Adicionar Novo Produto</h1>
    
    <?php if ($mensagem): ?>
        <div class="mensagem-erro"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <div class="form-group">
            <label for="nome">Nome do Produto</label>
            <input type="text" id="nome" name="nome" required>
        </div>
        
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="4" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="preco">Preço (R$)</label>
            <input type="number" id="preco" name="preco" step="0.01" min="0" required>
        </div>
        
        <div class="form-group">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria" required>
                <option value="bolos">Bolos</option>
                <option value="doces">Doces</option>
                <option value="kits">Kits Festa</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="imagem">Imagem do Produto</label>
            <input type="file" id="imagem" name="imagem" accept="image/*" required>
        </div>
        
        <div class="form-checkbox">
            <input type="checkbox" id="sugestao" name="sugestao">
            <label for="sugestao">Destacar como sugestão na página inicial</label>
        </div>
        
        <button type="submit" class="btn-primary">Cadastrar Produto</button>
    </form>
</main>

<?php include '../../../src/views/templates/admin-footer.php'; ?>