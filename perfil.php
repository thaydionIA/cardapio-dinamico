<?php
include('db/conexao.php'); // Inclui a conexão com o banco de dados via PDO
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

// Atualiza os dados do perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $foto = $user['foto']; // Mantém a foto existente por padrão

    // Se uma nova foto for enviada, processa o upload
    if (!empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto']['name'];
        $target_dir = "uploads/clientes/";
        $target_file = $target_dir . basename($foto);
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
    }

    // Atualiza os dados no banco de dados
    $sql = "UPDATE usuarios SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco, foto = :foto WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':id', $user_id);

    if ($stmt->execute()) {
        echo "Perfil atualizado com sucesso!";
        // Atualiza a sessão com o novo nome
        $_SESSION['user_nome'] = $nome;
        // Redireciona para o perfil para ver as mudanças
        header('Location: perfil.php');
        exit();
    } else {
        echo "Erro ao atualizar o perfil.";
    }
}
?>

<!-- Formulário de Perfil -->
<h2>Meu Perfil</h2>
<form action="perfil.php" method="POST" enctype="multipart/form-data">
    <img src="uploads/clientes/<?= htmlspecialchars($user['foto']); ?>" alt="Foto de Perfil" style="width: 150px; height: 150px;"><br>
    
    <label>Nome:</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($user['nome']); ?>" required><br>
    
    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required><br>
    
    <label>Telefone:</label>
    <input type="text" name="telefone" value="<?= htmlspecialchars($user['telefone']); ?>"><br>
    
    <label>Endereço:</label>
    <textarea name="endereco"><?= htmlspecialchars($user['endereco']); ?></textarea><br>
    
    <label>Foto de Perfil:</label>
    <input type="file" name="foto" accept="image/*"><br>
    
    <button type="submit">Atualizar Perfil</button>
</form>
