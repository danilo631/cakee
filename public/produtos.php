<?php
// Ativa a exibição de erros (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Configuração de paths
define('BASE_PATH', realpath(__DIR__ . '/../'));
require_once BASE_PATH . '/src/config/database.php';
require_once BASE_PATH . '/src/models/Produto.php';

use App\Models\Produto;



// Verifica autenticação
$usuarioLogado = $_SESSION['usuario'] ?? null;
$isVendedor = $usuarioLogado && $usuarioLogado['tipo'] === 'vendedor';

// Parâmetros da URL
$categoria = filter_input(INPUT_GET, 'categoria', FILTER_SANITIZE_STRING) ?? 'todos';
$pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?? 1;
$porPagina = 12;

try {
    if ($categoria === 'todos') {
        $produtos = Produto::listarTodos($pagina, $porPagina);
        $totalProdutos = Produto::contarTodos();
    } else {
        $produtos = Produto::listarPorCategoria($categoria, $pagina, $porPagina);
        $totalProdutos = Produto::contarPorCategoria($categoria);
    }
    
    $totalPaginas = ceil($totalProdutos / $porPagina);
    $categorias = Produto::listarCategorias();
} catch (Exception $e) {
    $erro = "Erro ao carregar produtos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos | Mac Cake</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/produtos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include BASE_PATH . '/src/views/templates/header.php'; ?>

    <main class="container produtos-page">
        <div class="page-header">
            <h1>
                <?= $categoria === 'todos' ? 'Todos os Produtos' : ucfirst($categoria) ?>
                <?php if ($isVendedor): ?>
                    <a href="admin/produtos/adicionar.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Adicionar Produto
                    </a>
                <?php endif; ?>
            </h1>
            
            <div class="breadcrumb">
                <a href="index.php">Home</a> &raquo;
                <span>Produtos</span>
            </div>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <div class="product-filters">
            <div class="filter-group">
                <label for="categoria">Categoria:</label>
                <select id="categoria" onchange="window.location.href='?categoria='+this.value">
                    <option value="todos" <?= $categoria === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat ?>" <?= $categoria === $cat ? 'selected' : '' ?>>
                        <?= ucfirst($cat) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="ordenar">Ordenar por:</label>
                <select id="ordenar">
                    <option value="preco-asc">Preço: Menor para Maior</option>
                    <option value="preco-desc">Preço: Maior para Menor</option>
                    <option value="nome-asc">Nome: A-Z</option>
                    <option value="nome-desc">Nome: Z-A</option>
                </select>
            </div>
        </div>

        <?php if (empty($produtos)): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>Nenhum produto encontrado nesta categoria</p>
                <a href="produtos.php" class="btn btn-primary">Ver Todos</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($produtos as $produto): ?>
                <div class="product-card">
                    <?php if ($produto['sugestao']): ?>
                        <div class="product-badge">
                            <span>Destaque</span>
                        </div>
                    <?php endif; ?>
                    
                    <a href="detalhes-produto.php?id=<?= $produto['id'] ?>">
                        <img src="<?= htmlspecialchars($produto['imagem']) ?>" 
                             alt="<?= htmlspecialchars($produto['nome']) ?>"
                             class="product-image">
                    </a>
                    
                    <div class="product-info">
                        <h3>
                            <a href="detalhes-produto.php?id=<?= $produto['id'] ?>">
                                <?= htmlspecialchars($produto['nome']) ?>
                            </a>
                        </h3>
                        
                        <p class="product-description">
                            <?= htmlspecialchars($produto['descricao']) ?>
                        </p>
                        
                        <div class="product-footer">
                            <span class="product-price">
                                R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                            </span>
                            
                            <div class="product-actions">
                                <?php if ($isVendedor): ?>
                                    <a href="admin/produtos/editar.php?id=<?= $produto['id'] ?>" 
                                       class="btn btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <button class="btn btn-cart" 
                                        onclick="addToCart(<?= $produto['id'] ?>)"
                                        title="Adicionar ao carrinho">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginação -->
            <?php if ($totalPaginas > 1): ?>
            <div class="pagination">
                <?php if ($pagina > 1): ?>
                    <a href="?categoria=<?= $categoria ?>&pagina=<?= $pagina-1 ?>" 
                       class="page-link">
                        &laquo; Anterior
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?categoria=<?= $categoria ?>&pagina=<?= $i ?>" 
                       class="page-link <?= $i == $pagina ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($pagina < $totalPaginas): ?>
                    <a href="?categoria=<?= $categoria ?>&pagina=<?= $pagina+1 ?>" 
                       class="page-link">
                        Próxima &raquo;
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <?php include BASE_PATH . '/src/views/templates/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
    function addToCart(productId) {
        <?php if ($usuarioLogado): ?>
            fetch('api/carrinho/adicionar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    produto_id: productId,
                    quantidade: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount();
                    showAlert('Produto adicionado ao carrinho!', 'success');
                } else {
                    showAlert(data.message || 'Erro ao adicionar', 'error');
                }
            });
        <?php else: ?>
            window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
        <?php endif; ?>
    }

    function updateCartCount() {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = parseInt(cartCount.textContent) + 1;
        } else {
            const countElement = document.createElement('span');
            countElement.id = 'cart-count';
            countElement.className = 'cart-count';
            countElement.textContent = '1';
            document.querySelector('.carrinho-container a').appendChild(countElement);
        }
    }

    function showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        document.body.prepend(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
    </script>
</body>
</html>