<?php
session_start();
require_once 'db/conexao.php'; // Ajuste o caminho conforme necessário

// Adicionar produto ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id']) && isset($_POST['quantidade'])) {
    $produto_id = $_POST['produto_id'];
    $quantidade = (int)$_POST['quantidade'];

    // Inicializar o carrinho se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    // Adicionar ou atualizar a quantidade do produto no carrinho
    if (array_key_exists($produto_id, $_SESSION['carrinho'])) {
        $_SESSION['carrinho'][$produto_id] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }

    echo "Produto ID: $produto_id foi adicionado ao carrinho com quantidade: $quantidade.<br>";
}

// Verifica se o carrinho está vazio
if (empty($_SESSION['carrinho'])) {
    echo "<h1>Carrinho Vazio</h1>";
    echo "<p>Você ainda não adicionou produtos ao carrinho.</p>";
} else {
    echo "<h1>Produtos no Carrinho</h1>";
    echo "<ul>";

    $valor_total = 0; // Inicializa o valor total

    // Loop através dos produtos no carrinho
    foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
        // Buscar as informações do produto usando o ID
        $stmt = $pdo->prepare("SELECT nome, preco FROM produtos WHERE id = :id");
        $stmt->bindParam(':id', $produto_id);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            $subtotal = $produto['preco'] * $quantidade; // Calcular subtotal
            $valor_total += $subtotal; // Adicionar ao valor total

            echo "<li>";
            echo htmlspecialchars($produto['nome']) . " - Quantidade: " . $quantidade . " - Preço Unitário: R$ " . number_format($produto['preco'], 2, ',', '.') . " - Subtotal: R$ " . number_format($subtotal, 2, ',', '.');
            echo "</li>";
        }
    }

    echo "</ul>";

    // Exibir o valor total da compra
    echo "<h2>Valor Total da Compra: R$ " . number_format($valor_total, 2, ',', '.') . "</h2>";
}
?>


