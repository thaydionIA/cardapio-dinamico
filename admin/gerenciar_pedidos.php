<?php 
// Incluir o header.php somente se o arquivo estiver sendo acessado diretamente
if (basename($_SERVER['PHP_SELF']) == 'gerenciar_pedidos.php') {
    include $_SERVER['DOCUMENT_ROOT'] . '/cardapio-dinamico/header-ad.php';
}

// Iniciar a sessão se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../db/conexao.php';

// Consultar pedidos no banco de dados
$stmt = $pdo->query("
    SELECT v.id, u.nome as cliente, v.total, v.status_pedido, v.status, v.data_venda 
    FROM vendas v
    JOIN usuarios u ON v.cliente_id = u.id
    ORDER BY v.data_venda DESC
");
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos</title>
    <link rel="stylesheet" href="assets/css/admin_style.css">
</head>
<body>
    <div class="admin-container">
        <h1>Gerenciar Pedidos</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Status Pedido</th>
                <th>Status Pagamento</th> <!-- Exibe o status de pagamento -->
                <th>Data da Venda</th>
                <th>Ação</th>
            </tr>
            <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                <td><?php echo htmlspecialchars($pedido['cliente']); ?></td>
                <td>R$<?php echo htmlspecialchars($pedido['total']); ?></td>
                <td><?php echo htmlspecialchars($pedido['status_pedido']); ?></td>
                <td><?php echo htmlspecialchars($pedido['status']); ?></td> <!-- Exibe o status do pagamento -->
                <td><?php echo htmlspecialchars($pedido['data_venda']); ?></td>
                <td>
                    <!-- Formulário para atualizar o status do pedido -->
                    <form method="POST" action="processa_pedido.php">
                        <input type="hidden" name="venda_id" value="<?php echo htmlspecialchars($pedido['id']); ?>">
                        <label for="status_pedido">Status Pedido:</label>
                        <select name="status_pedido">
                            <option value="Pedido Feito" <?php echo ($pedido['status_pedido'] == 'Pedido Feito') ? 'selected' : ''; ?>>Pedido Feito</option>
                            <option value="Em Preparo" <?php echo ($pedido['status_pedido'] == 'Em Preparo') ? 'selected' : ''; ?>>Em Preparo</option>
                            <option value="Saiu para Entrega" <?php echo ($pedido['status_pedido'] == 'Saiu para Entrega') ? 'selected' : ''; ?>>Saiu para Entrega</option>
                            <option value="Entregue" <?php echo ($pedido['status_pedido'] == 'Entregue') ? 'selected' : ''; ?>>Entregue</option>
                        </select>
                        <br>
                        <button type="submit" name="atualizar_status">Atualizar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <p><a href="index.php">Voltar ao Painel</a></p>
    </div>
</body>
</html>
<?php 
// Incluir o footer.php
include $_SERVER['DOCUMENT_ROOT'] . '/cardapio-dinamico/footer-ad.php'; 
?>
