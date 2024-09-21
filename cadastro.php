<?php
include('db/conexao.php'); // Inclui a conexão com o banco de dados via PDO

// Função para validar e cadastrar o cliente e o endereço de entrega
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT); // Criptografa a senha
    $telefone = $_POST['telefone'];
    $dd = $_POST['dd'];
    $foto = $_FILES['foto']['name'];

    // Dados do endereço
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $complemento = $_POST['complemento'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];

    // Diretório para salvar a foto do cliente
    $target_dir = "uploads/clientes/";
    $target_file = $target_dir . basename($foto);

    // Verifica se o upload da foto é uma imagem válida e move o arquivo
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        try {
            // Inicia a transação
            $pdo->beginTransaction();

            // Insere o cliente no banco de dados usando PDO
            $sql = "INSERT INTO usuarios (nome, email, cpf, senha, telefone, dd, foto) 
                    VALUES (:nome, :email, :cpf, :senha, :telefone, :dd, :foto)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':senha', $senha);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':dd', $dd);
            $stmt->bindParam(':foto', $foto);
            $stmt->execute();

            // Pega o ID do usuário inserido
            $usuario_id = $pdo->lastInsertId();

            // Insere o endereço de entrega do cliente
            $sql_endereco = "INSERT INTO enderecos_entrega (rua, numero, complemento, bairro, cidade, estado, cep, usuario_id) 
                             VALUES (:rua, :numero, :complemento, :bairro, :cidade, :estado, :cep, :usuario_id)";
            $stmt_endereco = $pdo->prepare($sql_endereco);
            $stmt_endereco->bindParam(':rua', $rua);
            $stmt_endereco->bindParam(':numero', $numero);
            $stmt_endereco->bindParam(':complemento', $complemento);
            $stmt_endereco->bindParam(':bairro', $bairro);
            $stmt_endereco->bindParam(':cidade', $cidade);
            $stmt_endereco->bindParam(':estado', $estado);
            $stmt_endereco->bindParam(':cep', $cep);
            $stmt_endereco->bindParam(':usuario_id', $usuario_id);
            $stmt_endereco->execute();

            // Confirma a transação
            $pdo->commit();

            // Redireciona para a página de login após o cadastro
            header('Location: login.php');
            exit();
        } catch (PDOException $e) {
            // Reverte a transação em caso de erro
            $pdo->rollBack();
            echo "Erro ao cadastrar: " . $e->getMessage();
        }
    } else {
        echo "Erro ao fazer upload da foto.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="/cardapio-dinamico/assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<header>
    <h1>Nome do Site</h1>
</header>

<main>
    <div class="cadastro-container">
        <h2>Cadastro de Usuário</h2>
        <form action="cadastro.php" method="POST" enctype="multipart/form-data" class="cadastro-form">
            <label>Nome:</label>
            <input type="text" name="nome" required>
            
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>CPF:</label>
            <input type="text" name="cpf" required>
            
            <label>Senha:</label>
            <input type="password" name="senha" required>
            
            <label>DDD:</label>
            <input type="text" name="dd" required>

            <label>Telefone:</label>
            <input type="text" name="telefone">

            <label>Foto de Perfil:</label>
            <input type="file" name="foto" accept="image/*">
            
            <h2>Endereço de Entrega</h2>
            
            <label>Rua:</label>
            <input type="text" name="rua" required>

            <label>Número:</label>
            <input type="text" name="numero" required>

            <label>Complemento:</label>
            <input type="text" name="complemento">

            <label>Bairro:</label>
            <input type="text" name="bairro" required>

            <label>Cidade:</label>
            <input type="text" name="cidade" required>

            <label>Estado:</label>
            <input type="text" name="estado" maxlength="2" required>

            <label>CEP:</label>
            <input type="text" name="cep" required>

            <button type="submit">Cadastrar</button>
        </form>
        <!-- Link para a página de login -->
        <p>Já tem uma conta? <a href="login.php">Faça login aqui</a></p>
    </div>
</main>

<footer>
    <p>&copy; 2024 Nome do Site. Todos os direitos reservados.</p>
</footer>

</body>
</html>


