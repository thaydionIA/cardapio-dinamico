<?php
session_start();

// Exibir uma mensagem para teste
echo "Arquivo adicionar.php está funcionando!<br>";

// Verificar se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se os dados do produto foram enviados
    if (isset($_POST['produto_id']) && isset($_POST['quantidade'])) {
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

        // Mensagem de sucesso
        echo "Produto ID: $produto_id foi adicionado ao carrinho com quantidade: $quantidade.<br>";
        
        // Adicione um botão para ir ao carrinho
        echo '<a href="carrinho.php">Ver Carrinho</a>';
    } else {
        echo "Dados do produto não foram enviados.<br>";
    }
} else {
    echo "Acesso inválido. Por favor, envie os dados via POST.<br>";
}

// Aqui você pode redirecionar ou continuar com a lógica necessária
// header('Location: /cardapio-dinamico/section/bebidas.php');
// exit();
?>
