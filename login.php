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

            // Exibe mensagem de sucesso e redireciona o usuário
            echo "Login realizado com sucesso!";
            header('Location: index.php'); // Redireciona para a página do painel ou outra desejada
            exit(); // Termina o script para evitar que o restante da página seja executado
        } else {
            echo "Email ou senha incorretos.";
        }
    } catch (PDOException $e) {
        echo "Erro ao realizar o login: " . $e->getMessage();
    }
}
?>

<!-- Formulário de Login -->
<form action="login.php" method="POST">
    <label>Email:</label>
    <input type="email" name="email" required>
    
    <label>Senha:</label>
    <input type="password" name="senha" required>
    
    <button type="submit">Entrar</button>
</form>
