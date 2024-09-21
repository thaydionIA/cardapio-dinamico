<?php
// Iniciar a sessão e incluir a conexão com o banco de dados
include('db/conexao.php');
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtém os dados do usuário
$sql = "SELECT * FROM usuarios WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtém o endereço de entrega
$sql_endereco = "SELECT * FROM enderecos_entrega WHERE usuario_id = :usuario_id";
$stmt_endereco = $pdo->prepare($sql_endereco);
$stmt_endereco->bindParam(':usuario_id', $user_id);
$stmt_endereco->execute();
$endereco = $stmt_endereco->fetch(PDO::FETCH_ASSOC);

// Atualiza os dados do perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $dd = $_POST['dd'];
    $foto = $user['foto']; // Mantém a foto existente por padrão
    $nova_senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Se uma nova foto for enviada, processa o upload
    if (!empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto']['name'];
        $target_dir = "uploads/clientes/";
        $target_file = $target_dir . basename($foto);
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
    }

    // Atualiza a senha apenas se as duas senhas forem iguais
    if ($nova_senha === $confirmar_senha) {
        $senha = password_hash($nova_senha, PASSWORD_BCRYPT);
    } else {
        echo "As senhas não coincidem.";
        exit();
    }

    // Atualiza os dados no banco de dados
    $sql = "UPDATE usuarios SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, dd = :dd, senha = :senha, foto = :foto WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':dd', $dd);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':id', $user_id);

    if ($stmt->execute()) {
        echo "Perfil atualizado com sucesso!";
        $_SESSION['user_nome'] = $nome;
        header('Location: perfil.php');
        exit();
    } else {
        echo "Erro ao atualizar o perfil.";
    }
}
?>

<?php
// Verificar se a variável global $incluir_rodape está definida e se deve incluir o rodapé
$incluir_rodape = !isset($GLOBALS['incluir_rodape']) || $GLOBALS['incluir_rodape'];

// Incluir o cabeçalho
include $_SERVER['DOCUMENT_ROOT'] . '/cardapio-dinamico/header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <!-- Ligando o arquivo CSS correto -->
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Caminho do CSS -->
</head>
<body>
    <div class="perfil-container">
        <h2>Meu Perfil</h2>
        <form class="perfil-form" action="perfil.php" method="POST" enctype="multipart/form-data">
            <img class="perfil-foto" src="uploads/clientes/<?= htmlspecialchars($user['foto']); ?>" alt="Foto de Perfil">
            
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($user['nome']); ?>" required>
            
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
            
            <label>CPF:</label>
            <input type="text" name="cpf" value="<?= htmlspecialchars($user['cpf']); ?>" required>
            
            <label>DDD:</label>
            <input type="text" name="dd" value="<?= htmlspecialchars($user['dd']); ?>" required>
            
            <label>Telefone:</label>
            <input type="text" name="telefone" value="<?= htmlspecialchars($user['telefone']); ?>">

            <h3>Endereço de Entrega</h3>
            <label>Rua:</label>
            <input type="text" name="rua" value="<?= htmlspecialchars($endereco['rua']); ?>" required>
            
            <label>Número:</label>
            <input type="text" name="numero" value="<?= htmlspecialchars($endereco['numero']); ?>" required>

            <label>Complemento:</label>
            <input type="text" name="complemento" value="<?= htmlspecialchars($endereco['complemento']); ?>">
            
            <label>Bairro:</label>
            <input type="text" name="bairro" value="<?= htmlspecialchars($endereco['bairro']); ?>" required>
            
            <label>Cidade:</label>
            <input type="text" name="cidade" value="<?= htmlspecialchars($endereco['cidade']); ?>" required>
            
            <label>Estado:</label>
            <input type="text" name="estado" value="<?= htmlspecialchars($endereco['estado']); ?>" maxlength="2" required>

            <label>CEP:</label>
            <input type="text" name="cep" value="<?= htmlspecialchars($endereco['cep']); ?>" required>

            <h3>Alterar Senha</h3>
            <label>Nova Senha:</label>
            <input type="password" name="senha">
            
            <label>Confirmar Senha:</label>
            <input type="password" name="confirmar_senha">
            
            <label>Foto de Perfil:</label>
            <input type="file" name="foto" accept="image/*">
            
            <button type="submit">Atualizar Perfil</button>
        </form>
    </div>

    <!-- Inclusão do rodapé -->
    <?php if ($incluir_rodape): ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/cardapio-dinamico/footer.php'; ?>
    <?php endif; ?>

</body>
</html>
