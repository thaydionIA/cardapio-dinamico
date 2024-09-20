<?php
// Verificar se a variável global $incluir_rodape está definida e se deve incluir o rodapé
$incluir_rodape = !isset($GLOBALS['incluir_rodape']) || $GLOBALS['incluir_rodape'];

if (basename($_SERVER['PHP_SELF']) == 'bebidas.php') {
    include $_SERVER['DOCUMENT_ROOT'] . '/cardapio-dinamico/header.php';
}
?>

<?php
require_once dirname(__DIR__) . '/db/conexao.php';
$base_url = '/cardapio-dinamico/admin/uploads/produtos/';

// Preparar a consulta de produtos da categoria "bebidas"
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE categoria = 'bebidas' ORDER BY nome");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bebidas</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>"> <!-- Cache busting -->
</head>
<body>
    <h1>Bebidas</h1>
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

                    <!-- Campo de quantidade com ícones de aumentar e diminuir -->
                    <div class="quantidade-container">
                        <button type="button" class="quantidade-btn diminuir">-</button>
                        <input type="number" name="quantidade" value="1" min="1" required class="quantidade-input">
                        <button type="button" class="quantidade-btn aumentar">+</button>
                    </div>

                    <!-- Botão de adicionar ao carrinho com a classe CSS -->
                    <button type="submit" class="adicionar-carrinho-btn">Adicionar ao Carrinho</button>
                </form>

            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Inclusão do rodapé se o arquivo estiver sendo acessado diretamente -->
    <?php if ($incluir_rodape): ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/cardapio-dinamico/footer.php'; ?>
    <?php endif; ?>

    <!-- Coloque o JavaScript aqui, antes do fechamento da tag body -->
    <script>
        document.querySelectorAll('.aumentar').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentNode.querySelector('.quantidade-input');
                input.value = parseInt(input.value) + 1;
            });
        });

        document.querySelectorAll('.diminuir').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentNode.querySelector('.quantidade-input');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            });
        });
    </script>
</body>
</html>
