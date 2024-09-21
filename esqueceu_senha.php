<?php
include('db/conexao.php'); // Inclui a conexão com o banco de dados via PDO

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os campos para validação
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $cpf = trim($_POST['cpf']);

    try {
        // Verifica se os dados fornecidos batem com os armazenados no banco de dados
        $sql = "SELECT * FROM usuarios WHERE email = :email AND telefone = :telefone AND cpf = :cpf";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se os dados conferirem, permite redefinir a senha
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: redefinir_senha.php'); // Redireciona para a página de redefinição
            exit();
        } else {
            $mensagem = "As informações não conferem. Tente novamente.";
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao processar a solicitação: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueceu a Senha</title>
    <link rel="stylesheet" href="/cardapio-dinamico/assets/css/style.css"> <!-- Incluindo seu CSS principal -->
</head>
<body>

<header>
    <h1>Recuperar Senha</h1>
</header>

<main>
    <div class="login-container">
        <h2>Verifique sua Identidade</h2>

        <?php if ($mensagem): ?>
            <div class="error"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form action="esqueceu_senha.php" method="POST" class="login-form">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="input-field" placeholder="Ex: exemplo@dominio.com" required>
            </div>
            <div>
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" class="input-field" placeholder="Ex: 62999999999" required>
            </div>
            <div>
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" class="input-field" placeholder="Ex: 12345678909" required>
            </div>
            <button type="submit">Verificar</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2024 Sistema de Login</p>
</footer>

</body>
</html>
