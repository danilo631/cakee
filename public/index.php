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
require_once BASE_PATH . '/src/models/Usuario.php';

use App\Models\Produto;
use App\Models\Usuario;

// Verifica autenticação
$usuarioLogado = $_SESSION['usuario'] ?? null;
$isVendedor = $usuarioLogado && $usuarioLogado['tipo'] === 'vendedor';

// Carrega dados iniciais
try {
    $produtosSugeridos = Produto::listarSugestoes();
    $categorias = Produto::listarCategorias();
} catch (Exception $e) {
    $erro = "Erro ao carregar produtos: " . $e->getMessage();
}

// Verifica mensagens flash
$mensagem = $_SESSION['mensagem'] ?? null;
if ($mensagem) {
    unset($_SESSION['mensagem']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mac Cake - Confeitaria Artesanal</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include BASE_PATH . '/src/views/templates/header.php'; ?>

    <!-- Notificação Flash -->
    <?php if ($mensagem): ?>
    <div class="flash-message">
        <?= htmlspecialchars($mensagem) ?>
        <span class="close-flash">&times;</span>
    </div>
    <?php endif; ?>

    <main class="container">
        <!-- Banner Hero -->
        <section class="hero-banner" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/images/banner-bg.jpg')">
            <div class="hero-content">
                <h1>Confeitaria Artesanal com Amor</h1>
                <p>Bolos, doces e kits festa feitos com ingredientes selecionados</p>
                <div class="hero-buttons">
                    <a href="produtos.php" class="btn btn-primary">Ver Produtos</a>
                    <?php if ($isVendedor): ?>
                        <a href="admin/produtos/adicionar.php" class="btn btn-secondary">
                            <i class="fas fa-plus"></i> Adicionar Produto
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Produtos em Destaque -->
        <section class="featured-products">
            <div class="section-header">
                <h2><i class="fas fa-star"></i> Nossos Destaques</h2>
                <a href="produtos.php" class="see-all">Ver todos <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>

            <?php if (empty($produtosSugeridos)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>Nenhum produto em destaque no momento</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($produtosSugeridos as $produto): ?>
                    <div class="product-card">
                        <?php if ($produto['sugestao']): ?>
                            <div class="product-badge">
                                <span>Destaque</span>
                            </div>
                        <?php endif; ?>
                        
                        <a href="produto.php?id=<?= $produto['id'] ?>" class="product-link">
                            <img src="<?= htmlspecialchars($produto['imagem']) ?>" 
                                alt="<?= htmlspecialchars($produto['nome']) ?>"
                                class="product-image" loading="lazy">
                            <div class="product-info">
                                <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                                <p class="product-description">
                                    <?= htmlspecialchars(mb_strimwidth($produto['descricao'], 0, 100, '...')) ?>
                                </p>
                                <div class="product-footer">
                                    <span class="product-price">
                                        R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                                    </span>
                                    <div class="product-actions">
                                        <button class="btn btn-cart" 
                                                onclick="addToCart(event, <?= $produto['id'] ?>)">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Categorias -->
        <section class="categories">
            <h2><i class="fas fa-list"></i> Nossas Categorias</h2>
            <div class="categories-grid">
                <?php foreach ($categorias as $categoria): ?>
                <a href="produtos.php?categoria=<?= urlencode($categoria) ?>" 
                   class="category-card">
                    <div class="category-icon">
                        <?php if ($categoria === 'bolos'): ?>
                            <i class="fas fa-birthday-cake"></i>
                        <?php elseif ($categoria === 'doces'): ?>
                            <i class="fas fa-candy-cane"></i>
                        <?php else: ?>
                            <i class="fas fa-gift"></i>
                        <?php endif; ?>
                    </div>
                    <h3><?= ucfirst($categoria) ?></h3>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Depoimentos -->
        <section class="testimonials">
            <h2><i class="fas fa-quote-left"></i> O que dizem sobre nós</h2>
            <div class="testimonials-slider">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Os bolos da Mac Cake são incríveis! Sempre encomendo para meus eventos."</p>
                    </div>
                    <div class="testimonial-author">
                        <strong>Ana Silva</strong>
                        <span>Cliente há 2 anos</span>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Adoro a qualidade dos produtos e o atendimento personalizado."</p>
                    </div>
                    <div class="testimonial-author">
                        <strong>Carlos Oliveira</strong>
                        <span>Cliente há 1 ano</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include BASE_PATH . '/src/views/templates/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
    // Função para adicionar ao carrinho
    function addToCart(event, productId) {
        event.preventDefault();
        event.stopPropagation();
        
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
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Erro na comunicação com o servidor', 'error');
            });
        <?php else: ?>
            window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
        <?php endif; ?>
    }

    // Atualiza contador do carrinho
    function updateCartCount() {
        fetch('api/carrinho/contar.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.count;
                    } else if (data.count > 0) {
                        const cartLink = document.querySelector('.carrinho-container a');
                        if (cartLink) {
                            const countBadge = document.createElement('span');
                            countBadge.id = 'cart-count';
                            countBadge.className = 'cart-count';
                            countBadge.textContent = data.count;
                            cartLink.appendChild(countBadge);
                        }
                    }
                }
            });
    }

    // Mostra alerta flutuante
    function showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            <span>${message}</span>
            <button onclick="this.parentElement.remove()">&times;</button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    // Fecha mensagem flash
    document.querySelector('.close-flash')?.addEventListener('click', function() {
        this.parentElement.remove();
    });

    // Carrega contador do carrinho ao iniciar
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($usuarioLogado): ?>
            updateCartCount();
        <?php endif; ?>
    });
    </script>
</body>
</html>