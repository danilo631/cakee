// Menu mobile
function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
    
    // Adiciona overlay quando o menu está aberto
    if (nav.classList.contains('active')) {
        const overlay = document.createElement('div');
        overlay.className = 'overlay';
        overlay.onclick = toggleMenu;
        document.body.appendChild(overlay);
    } else {
        document.querySelector('.overlay')?.remove();
    }
}

function closeMenu() {
    document.getElementById('mainNav').classList.remove('active');
    document.querySelector('.overlay')?.remove();
}

// Atualizar contador do carrinho
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se o usuário está logado
    if (typeof usuarioId !== 'undefined') {
        fetch('../api/carrinho/contar.php?usuario_id=' + usuarioId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.count > 0) {
                    const cartCount = document.createElement('span');
                    cartCount.id = 'cart-count';
                    cartCount.className = 'cart-count';
                    cartCount.textContent = data.count;
                    document.querySelector('.carrinho-container a').appendChild(cartCount);
                }
            });
    }
    
    // Exibir mensagens flash
    if (sessionStorage.getItem('mensagem')) {
        const mensagemDiv = document.createElement('div');
        mensagemDiv.className = 'flash-message';
        mensagemDiv.textContent = sessionStorage.getItem('mensagem');
        document.body.prepend(mensagemDiv);
        sessionStorage.removeItem('mensagem');
        
        setTimeout(() => {
            mensagemDiv.style.opacity = '0';
            setTimeout(() => mensagemDiv.remove(), 500);
        }, 3000);
    }
});

// Adicionar ao carrinho
function adicionarAoCarrinho(produtoId) {
    if (typeof usuarioId === 'undefined') {
        window.location.href = 'login.php';
        return;
    }
    
    fetch('../api/carrinho/adicionar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            usuario_id: usuarioId,
            produto_id: produtoId,
            quantidade: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar contador do carrinho
            let cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = parseInt(cartCount.textContent) + 1;
            } else {
                cartCount = document.createElement('span');
                cartCount.id = 'cart-count';
                cartCount.className = 'cart-count';
                cartCount.textContent = '1';
                document.querySelector('.carrinho-container a').appendChild(cartCount);
            }
            
            // Mostrar mensagem de sucesso
            sessionStorage.setItem('mensagem', 'Produto adicionado ao carrinho!');
            window.location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao adicionar ao carrinho');
    });
}