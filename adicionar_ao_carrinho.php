<?php
require_once 'db/conexao.php'; // Ajuste o caminho conforme necessário

// Inicializar o carrinho na sessão, se não estiver já inicializado
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adicionar produto ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id']) && isset($_POST['quantidade'])) {
    $produto_id = $_POST['produto_id'];
    $quantidade = (int)$_POST['quantidade'];

    // Atualizar a quantidade do produto no carrinho
    if (array_key_exists($produto_id, $_SESSION['carrinho'])) {
        $_SESSION['carrinho'][$produto_id] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }

    // Salvar ou atualizar no banco de dados
    $usuario_id = $_SESSION['user_id'];
    try {
        // Verifica se o produto já está no banco para atualizar a quantidade
        $stmt = $pdo->prepare("SELECT quantidade FROM carrinho WHERE usuario_id = :usuario_id AND produto_id = :produto_id");
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':produto_id', $produto_id);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            // Atualizar a quantidade no banco de dados
            $nova_quantidade = $resultado['quantidade'] + $quantidade;
            $stmt_update = $pdo->prepare("UPDATE carrinho SET quantidade = :quantidade WHERE usuario_id = :usuario_id AND produto_id = :produto_id");
            $stmt_update->bindParam(':quantidade', $nova_quantidade);
            $stmt_update->bindParam(':usuario_id', $usuario_id);
            $stmt_update->bindParam(':produto_id', $produto_id);
            $stmt_update->execute();
        } else {
            // Inserir novo produto no carrinho do banco de dados
            $stmt_insert = $pdo->prepare("INSERT INTO carrinho (usuario_id, produto_id, quantidade) VALUES (:usuario_id, :produto_id, :quantidade)");
            $stmt_insert->bindParam(':usuario_id', $usuario_id);
            $stmt_insert->bindParam(':produto_id', $produto_id);
            $stmt_insert->bindParam(':quantidade', $quantidade);
            $stmt_insert->execute();
        }

        // Recuperar o nome do produto
        $stmt_nome = $pdo->prepare("SELECT nome FROM produtos WHERE id = :id");
        $stmt_nome->bindParam(':id', $produto_id);
        $stmt_nome->execute();
        $produto = $stmt_nome->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            echo "Produto " . htmlspecialchars($produto['nome']) . " foi adicionado ao carrinho com quantidade: $quantidade.<br>";
        } else {
            echo "Produto não encontrado.";
        }
    } catch (PDOException $e) {
        echo "Erro ao adicionar produto ao carrinho: " . $e->getMessage();
    }
}
