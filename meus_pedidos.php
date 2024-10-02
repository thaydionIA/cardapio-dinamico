<?php 
// Incluir o header.php ou garantir que a sessão está ativa
include 'header.php';

// Iniciar a sessão se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {  // Usando 'user_id' como no exemplo do index.php
    header("Location: login.php");
    exit();
}

require_once 'db/conexao.php';

// Obter o ID do usuário logado da sessão
$usuario_id = $_SESSION['user_id'];  // Usar 'user_id', assim como feito no index.php

// Consultar pedidos feitos pelo usuário logado na tabela vendas
$stmt = $pdo->prepare("
    SELECT id, total, status_pedido, status, data_venda 
    FROM vendas
    WHERE cliente_id = ?
    ORDER BY data_venda DESC
");
$stmt->execute([$usuario_id]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="padding: 30px; background-color: #1c1c1c; color: #d4af37; border-radius: 10px; max-width: 1000px; margin: 0 auto;">
        <h1 style="text-align: center; margin-bottom: 30px;">Meus Pedidos</h1>
        <table style="width: 100%; border-collapse: collapse; background-color: #f0d28b; border-radius: 8px; overflow: hidden; text-align: center;">
            <tr>
                <th style="padding: 15px; background-color: #1c1c1c; color: #d4af37; width: 10%;">ID do Pedido</th>
                <th style="padding: 15px; background-color: #1c1c1c; color: #d4af37; width: 15%;">Total</th>
                <th style="padding: 15px; background-color: #1c1c1c; color: #d4af37; width: 20%;">Status Pedido</th>
                <th style="padding: 15px; background-color: #1c1c1c; color: #d4af37; width: 20%;">Status Pagamento</th>
                <th style="padding: 15px; background-color: #1c1c1c; color: #d4af37; width: 35%;">Data da Venda</th>
            </tr>
            <?php if (!empty($pedidos)): ?>
                <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;"><?php echo htmlspecialchars($pedido['id']); ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">R$<?php echo htmlspecialchars($pedido['total']); ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;"><?php echo htmlspecialchars($pedido['status_pedido']); ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;"><?php echo htmlspecialchars($pedido['status']); ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;"><?php echo htmlspecialchars($pedido['data_venda']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="padding: 15px; text-align: center;">Você ainda não fez nenhum pedido.</td>
                </tr>
            <?php endif; ?>
        </table>
        <p style="text-align: center; margin-top: 20px;">
            <a href="index.php" style="color: #d4af37; text-decoration: none;">Voltar à página inicial</a>
        </p>
    </div>
    <!-- Inclui o arquivo de JavaScript centralizado -->
    <script src="assets/js/script.js"></script>
</body>
</html>
<?php 
// Incluir o footer.php
include 'footer.php'; 
?>