<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/models/Usuario.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'] ?? 'cliente';
    
    $usuario = new Usuario($nome, $email, $senha, $tipo);
    $resultado = Usuario::cadastrar($usuario);
    
    if ($resultado['success']) {
        $_SESSION['mensagem'] = 'Cadastro realizado com sucesso! Faça login para continuar.';
        header('Location: login.php');
        exit;
    } else {
        $mensagem = $resultado['message'];
    }
}

include '../src/views/templates/header.php';
?>

<main class="container auth-page">
    <div class="auth-form">
        <h1>Cadastre-se</h1>
        
        <?php if ($mensagem): ?>
            <div class="mensagem-erro"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="tipo">Você é:</label>
                <select id="tipo" name="tipo">
                    <option value="cliente">Cliente</option>
                    <option value="vendedor">Vendedor</option>
                </select>
            </div>
            
            <button type="submit" class="btn-primary">Cadastrar</button>
        </form>
        
        <div class="auth-links">
            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
        </div>
    </div>
</main>

<?php include '../src/views/templates/footer.php'; ?>