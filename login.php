<?php
include('db/conexao.php'); // Inclui a conexão com o banco de dados via PDO

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para validar o login do cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    try {
        // Busca o usuário no banco de dados usando PDO
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o usuário foi encontrado e se a senha está correta
        if ($user && password_verify($senha, $user['senha'])) {
            // Define as variáveis de sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];

            // Redireciona o usuário para a página inicial
            header('Location: index.php'); 
            exit(); // Termina o script para evitar que o restante da página seja executado
        } else {
            // Caso o login falhe, exibe mensagem de erro
            echo "<div class='error'>Email ou senha incorretos.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'>Erro ao realizar o login: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Cache busting com time() para garantir que o CSS mais recente seja carregado -->
    <link rel="stylesheet" href="/cardapio-dinamico/assets/css/style.css?v=<?php echo time(); ?>"> <!-- Cache busting -->
</head>
<body>

<header>
    <h1>Bem-vindo ao Sistema</h1>
</header>

<main>
    <div class="login-container">
        <h2>Faça seu Login</h2>
        <form action="login.php" method="POST" class="login-form">
            <div>
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            
            <div>
                <label>Senha:</label>
                <input type="password" name="senha" required>
            </div>
            
            <button type="submit">Entrar</button>

            <!-- Link de cadastro com o caminho correto -->
            <p class="no-account">Não tem uma conta? <a href="/cardapio-dinamico/cadastro.php">Cadastre-se</a></p>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2024 Sistema de Login</p>
</footer>

</body>
</html>
