<?php
include('db/conexao.php'); // Inclui a conexão com o banco de dados via PDO

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$mensagem = '';

if (!isset($_SESSION['user_id'])) {
    header('Location: esqueceu_senha.php'); // Redireciona de volta se o usuário não estiver autenticado
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nova_senha = trim($_POST['senha']);
    $confirmar_senha = trim($_POST['confirmar_senha']);

    // Verifica se as senhas coincidem
    if ($nova_senha === $confirmar_senha) {
        $senha_hash = password_hash($nova_senha, PASSWORD_BCRYPT);

        try {
            // Atualiza a senha do usuário no banco de dados
            $sql = "UPDATE usuarios SET senha = :senha WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':senha', $senha_hash);
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();

            $mensagem = "Senha redefinida com sucesso!";
            session_destroy(); // Finaliza a sessão após redefinir a senha
        } catch (PDOException $e) {
            $mensagem = "Erro ao redefinir a senha: " . $e->getMessage();
        }
    } else {
        $mensagem = "As senhas não coincidem. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="/cardapio-dinamico/assets/css/style.css"> <!-- Incluindo seu CSS principal -->
</head>
<body>

<header>
    <h1>Redefinir Senha</h1>
</header>

<main>
    <div class="login-container">
        <h2>Insira sua nova senha</h2>

        <?php if ($mensagem): ?>
            <div class="error"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <?php if (!empty($mensagem) && $mensagem == "Senha redefinida com sucesso!"): ?>
            <p><a href="login.php">Voltar para o login</a></p>
        <?php else: ?>
            <form action="redefinir_senha.php" method="POST" class="login-form">
                <div>
                    <label for="senha">Nova Senha:</label>
                    <input type="password" id="senha" name="senha" placeholder="Ex: SenhaSegura123!" required>
                </div>
                <div>
                    <label for="confirmar_senha">Confirmar Senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a nova senha" required>
                </div>
                <button type="submit">Redefinir Senha</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2024 Sistema de Login</p>
</footer>

</body>
</html>