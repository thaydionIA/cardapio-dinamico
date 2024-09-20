<?php 
// Incluir o header.php somente se o arquivo estiver sendo acessado diretamente
if (basename($_SERVER['PHP_SELF']) == 'sobremesas.php') {
    include $_SERVER['DOCUMENT_ROOT'] . '/cardapio-dinamico/header.php';
}
?> 
<?php
require_once dirname(__DIR__) . '/db/conexao.php';
$base_url = '/cardapio-dinamico/admin/uploads/produtos/';

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE categoria = 'sobremesas' ORDER BY nome");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobremesas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Sobremesas</h1>
    <div class="produtos-container">
    <?php foreach ($produtos as $produto): ?>
    <div class="produto-item">
        <div class="produto-imagem">
            <?php if ($produto['imagem']): ?>
                <!-- Caminho absoluto para a imagem -->
                <img src="<?php echo $base_url . htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
            <?php endif; ?>
        </div>
        <div class="produto-info">
            <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
            <p><?php echo htmlspecialchars($produto['descricao']); ?></p>
            <p class="preco">Preço: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
            <!-- Formulário para adicionar ao carrinho -->
            <form action="/cardapio-dinamico/carrinho.php" method="POST">
                    <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                    
                    <!-- Campo de quantidade -->
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" name="quantidade" value="1" min="1" required>
                    
                    <!-- Botão de adicionar ao carrinho -->
                    <button type="submit">Adicionar ao Carrinho</button>
                </form>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
</body>
</html>
<?php 
if (basename($_SERVER['PHP_SELF']) == 'sobremesas.php') {
    include $_SERVER['DOCUMENT_ROOT'] . '/cardapio-dinamico/footer.php';
}
?>

