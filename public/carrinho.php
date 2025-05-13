<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/models/Carrinho.php';
require_once '../src/models/Produto.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuarioId = $_SESSION['usuario']['id'];
$itensCarrinho = Carrinho::listarItens($usuarioId);

// Calcular total
$total = 0;
foreach ($itensCarrinho as $item) {
    $total += $item['subtotal'];
}

// Processar remoção de item
if (isset($_GET['remover'])) {
    $produtoId = $_GET['remover'];
    Carrinho::removerItem($usuarioId, $produtoId);
    header('Location: carrinho.php');
    exit;
}

// Processar finalização de compra
if (isset($_POST['finalizar'])) {
    // Aqui você implementaria a lógica de finalização
    // Criar pedido, limpar carrinho, etc.
    Carrinho::limparCarrinho($usuarioId);
    $_SESSION['mensagem'] = 'Compra finalizada com sucesso!';
    header('Location: obrigado.php');
    exit;
}

include '../src/views/templates/header.php';
?>

<main class="container carrinho-page">
    <h1>Seu Carrinho de Compras</h1>
    
    <?php if (empty($itensCarrinho)): ?>
        <div class="carrinho-vazio">
            <i class="fas fa-shopping-cart"></i>
            <p>Seu carrinho está vazio</p>
            <a href="produtos.php" class="btn-primary">Continuar Comprando</a>
        </div>
    <?php else: ?>
        <div class="carrinho-itens">
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itensCarrinho as $item): ?>
                    <tr>
                        <td>
                            <div class="produto-info">
                                <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>">
                                <div>
                                    <h3><?= htmlspecialchars($item['nome']) ?></h3>
                                    <p><?= htmlspecialchars($item['descricao']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                        <td><?= $item['quantidade'] ?></td>
                        <td>R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
                        <td>
                            <a href="?remover=<?= $item['id'] ?>" class="btn-remover">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="carrinho-total">
                <h3>Total: <span>R$ <?= number_format($total, 2, ',', '.') ?></span></h3>
                <form method="POST">
                    <button type="submit" name="finalizar" class="btn-primary btn-finalizar">
                        Finalizar Compra <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include '../src/views/templates/footer.php'; ?>