<?php
include('db/conexao.php'); // Inclui a conexão com o banco de dados via PDO

// Função para validar e cadastrar o cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT); // Criptografa a senha
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $foto = $_FILES['foto']['name'];

    // Diretório para salvar a foto do cliente
    $target_dir = "uploads/clientes/";
    $target_file = $target_dir . basename($foto);

    // Verifica se o upload da foto é uma imagem válida e move o arquivo
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        try {
            // Insere o cliente no banco de dados usando PDO
            $sql = "INSERT INTO usuarios (nome, email, senha, telefone, endereco, foto) VALUES (:nome, :email, :senha, :telefone, :endereco, :foto)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':foto', $foto);

            if ($stmt->execute()) {
                // Redireciona para a página de login após o cadastro
                header('Location: login.php');
                exit();
            } else {
                echo "Erro ao cadastrar.";
            }
        } catch (PDOException $e) {
            echo "Erro ao cadastrar: " . $e->getMessage();
        }
    } else {
        echo "Erro ao fazer upload da foto.";
    }
}
?>

<!-- Formulário de Cadastro -->
<form action="cadastro.php" method="POST" enctype="multipart/form-data">
    <label>Nome:</label>
    <input type="text" name="nome" required>
    
    <label>Email:</label>
    <input type="email" name="email" required>
    
    <label>Senha:</label>
    <input type="password" name="senha" required>
    
    <label>Telefone:</label>
    <input type="text" name="telefone">
    
    <label>Endereço:</label>
    <textarea name="endereco"></textarea>
    
    <label>Foto de Perfil:</label>
    <input type="file" name="foto" accept="image/*">
    
    <button type="submit">Cadastrar</button>
</form>

<!-- Link para a página de Login -->
<p>Já tem uma conta? <a href="login.php">Faça login aqui</a></p>
