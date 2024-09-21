<?php
session_start();
require_once 'db/conexao.php'; // Ajuste o caminho conforme necessário
include $_SERVER['DOCUMENT_ROOT'] . '/cardapio-dinamico/header.php'; // Inclua o cabeçalho se necessário

// Verifica se o usuário está logado e se o carrinho não está vazio
if (!isset($_SESSION['user_id']) || empty($_SESSION['carrinho'])) {
    echo "<h1>Você precisa estar logado e ter itens no carrinho para realizar a compra.</h1>";
    require_once 'footer.php'; // Inclui o rodapé
    exit;
}

$valor_total = 0; // Calcula o valor total do carrinho
foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
    // Consultar o preço do produto no banco de dados
    $stmt = $pdo->prepare("SELECT preco FROM produtos WHERE id = :id");
    $stmt->bindParam(':id', $produto_id);
    $stmt->execute();
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        $subtotal = $produto['preco'] * $quantidade;
        $valor_total += $subtotal;
    }
}

// Verifica se o formulário foi enviado e redireciona para a API correta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forma_pagamento'])) {
    $forma_pagamento = $_POST['forma_pagamento'];

    // URL base da sua API
    $url_api = '/cardapio-dinamico/API-cred_PagSeguro/views/index.php';
    
    // Redireciona para o index.php da API com os dados de pagamento
    if ($forma_pagamento === 'pix' || $forma_pagamento === 'credito') {
        // Redirecionar para a API passando os parâmetros necessários (forma de pagamento, valor total e user_id)
        $url_pagamento = $url_api . '?valor=' . urlencode($valor_total) . '&user_id=' . urlencode($_SESSION['user_id']) . '&forma_pagamento=' . urlencode($forma_pagamento);
        header("Location: $url_pagamento");
        exit;
    } else {
        echo "<h1>Forma de pagamento inválida. Por favor, selecione uma opção válida.</h1>";
        exit;
    }
}

?>

<h1>Escolha a Forma de Pagamento</h1>
<p>Valor Total da Compra: R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></p>

<form action="realizar_compra.php" method="post">
    <input type="hidden" name="valor_total" value="<?php echo $valor_total; ?>">
    <label>
        <input type="radio" name="forma_pagamento" value="pix" required>
        Pagamento via PIX
    </label>
    <br>
    <label>
        <input type="radio" name="forma_pagamento" value="credito" required>
        Pagamento via Cartão de Crédito
    </label>
    <br><br>
    <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; font-size: 16px; cursor: pointer;">Confirmar Pagamento</button>
</form>

<?php
include 'footer.php'; // Inclua o rodapé se necessário
?>
