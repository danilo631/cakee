<?php
session_start();
require_once '../src/config/database.php';
require_once '../src/models/Usuario.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    $usuario = Usuario::autenticar($email, $senha);
    
    if ($usuario) {
        $_SESSION['usuario'] = $usuario;
        
        // Redirecionar com base no tipo de usuário
        if ($usuario['tipo'] === 'vendedor') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $mensagem = 'Email ou senha incorretos';
    }
}

include '../src/views/templates/header.php';
?>

<main class="container auth-page">
    <div class="auth-form">
        <h1>Login</h1>
        
        <?php if ($mensagem): ?>
            <div class="mensagem-erro"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <button type="submit" class="btn-primary">Entrar</button>
        </form>
        
        <div class="auth-links">
            <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
            <p><a href="recuperar-senha.php">Esqueceu sua senha?</a></p>
        </div>
    </div>
</main>

<?php include '../src/views/templates/footer.php'; ?>