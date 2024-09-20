<?php
session_start();
require_once 'db/conexao.php'; // Ajuste o caminho conforme necessário
require_once 'header.php'; // Inclui o cabeçalho

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo "<h1>Você precisa estar logado para ver o carrinho.</h1>";
    require_once 'footer.php'; // Inclui o rodapé
    exit; // Sai se não estiver logado
}

// Inicializar o carrinho na sessão
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adicionar produto ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id']) && isset($_POST['quantidade'])) {
    $produto_id = $_POST['produto_id'];
    $quantidade = (int)$_POST['quantidade'];

    // Adicionar ou atualizar a quantidade do produto no carrinho
    if (array_key_exists($produto_id, $_SESSION['carrinho'])) {
        $_SESSION['carrinho'][$produto_id] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }

    // Salvar no banco de dados
    $usuario_id = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("INSERT INTO carrinho_compras (usuario_id, produto_id, quantidade) VALUES (:usuario_id, :produto_id, :quantidade)");
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':produto_id', $produto_id);
        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Erro ao adicionar produto ao carrinho: " . $e->getMessage();
    }

    echo "Produto ID: $produto_id foi adicionado ao carrinho com quantidade: $quantidade.<br>";
}

// Remover produto do carrinho
if (isset($_GET['remove_id'])) {
    $remove_id = $_GET['remove_id'];

    // Remove do carrinho na sessão
    if (array_key_exists($remove_id, $_SESSION['carrinho'])) {
        unset($_SESSION['carrinho'][$remove_id]);

        // Remover do banco de dados
        $usuario_id = $_SESSION['user_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM carrinho_compras WHERE usuario_id = :usuario_id AND produto_id = :produto_id");
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':produto_id', $remove_id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao remover produto do carrinho: " . $e->getMessage();
        }
    }
}

// Buscar produtos do carrinho no banco de dados
$usuario_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT produto_id, quantidade FROM carrinho_compras WHERE usuario_id = :usuario_id");
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $carrinho_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preenche a variável de sessão com os dados do banco
    foreach ($carrinho_db as $item) {
        $_SESSION['carrinho'][$item['produto_id']] = $item['quantidade'];
    }
} catch (PDOException $e) {
    echo "Erro ao buscar produtos do carrinho: " . $e->getMessage();
}

// Verifica se o carrinho está vazio
if (empty($_SESSION['carrinho'])) {
    echo "<h1>Carrinho Vazio</h1>";
    echo "<p>Você ainda não adicionou produtos ao carrinho.</p>";
} else {
    echo "<h1>Produtos no Carrinho</h1>";
    echo "<div class='produtos-container'>"; // Início do container de produtos

    $valor_total = 0; // Inicializa o valor total

    // Loop através dos produtos no carrinho
    foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
        // Buscar as informações do produto usando o ID
        $stmt = $pdo->prepare("SELECT nome, preco, imagem FROM produtos WHERE id = :id");
        $stmt->bindParam(':id', $produto_id);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            $subtotal = $produto['preco'] * $quantidade; // Calcular subtotal
            $valor_total += $subtotal; // Adicionar ao valor total

            // Exibir produto com layout estilizado
            echo "<div class='produto-item'>";
            echo "<div class='produto-imagem'>";
            if ($produto['imagem']) {
                echo "<img src='/cardapio-dinamico/admin/uploads/produtos/" . htmlspecialchars($produto['imagem']) . "' alt='" . htmlspecialchars($produto['nome']) . "'>";
            }
            echo "</div>";
            echo "<div class='produto-info'>";
            echo "<h3>" . htmlspecialchars($produto['nome']) . "</h3>";
            echo "<p>Quantidade: " . $quantidade . "</p>";
            echo "<p>Preço Unitário: R$ " . number_format($produto['preco'], 2, ',', '.') . "</p>";
            echo "<p>Subtotal: R$ " . number_format($subtotal, 2, ',', '.') . "</p>";
            echo "<a href='?remove_id=" . $produto_id . "' class='remover-produto' title='Remover'>✖</a>"; // Ícone de remover
            echo "</div>";
            echo "</div>";
        }
    }

    echo "</div>"; // Fim do container de produtos

    // Exibir o valor total da compra
    echo "<h2>Valor Total da Compra: R$ " . number_format($valor_total, 2, ',', '.') . "</h2>";
}

require_once 'footer.php'; // Inclui o rodapé
?>
