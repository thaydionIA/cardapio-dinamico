<?php
include('db/conexao.php'); // Inclui a conexão com o banco de dados via PDO

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$mensagem = '';
$erros = []; // Array para armazenar os erros específicos dos campos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os campos para validação
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $cpf = trim($_POST['cpf']);

    try {
        // Verifica se o email existe
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verifica se o email não corresponde
        if (!$user) {
            $erros[] = 'Campo Email não corresponde com os dados do cadastro.';
        }
        // Verifica se o telefone não corresponde, mesmo que o email não corresponda
        if (!$user || ($user && $user['telefone'] !== $telefone)) {
            $erros[] = 'Campo Telefone não corresponde com os dados do cadastro.';
        }

        // Verifica se o CPF não corresponde, mesmo que o email e o telefone não correspondam
        if (!$user || ($user && $user['cpf'] !== $cpf)) {
            $erros[] = 'Campo CPF não corresponde com os dados do cadastro.';
        }

        // Exibe mensagem única se todos os campos estiverem errados
        if (count($erros) === 3) {
            $mensagem = "Os dados informados não correspondem aos dados do cadastro.";
        } else if (!empty($erros)) {
            // Junta as mensagens de erro
            $mensagem = implode('<br>', $erros);
        } else {
            // Se não houver erros, prossegue para redefinir a senha
            $_SESSION['user_id'] = $user['id'];
            header('Location: redefinir_senha.php'); // Redireciona para a página de redefinição
            exit();
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao processar a solicitação: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @media (max-width: 768px) {
            .container {
                padding: 15px;
                max-width: 100%;
            }

            /* Exibir os dados da tabela como lista em telas menores */
            table, thead, tbody, th, td, tr {
                display: block;
            }

            /* Esconder os cabeçalhos */
            thead tr {
                display: none;
            }

            /* Definir estilo para cada linha da tabela */
            tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 8px;
            }

            td {
                border: none;
                padding: 10px 15px;
                position: relative;
                text-align: right;
            }

            /* Exibir o rótulo correspondente a cada célula */
            td:before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                font-weight: bold;
                color: #1c1c1c;
                text-transform: uppercase;
            }
        }
    </style>
</head>
<body>
    <div class="container" style="padding: 30px; background-color: #1c1c1c; color: #d4af37; border-radius: 10px; max-width: 1000px; margin: 0 auto;">
        <h1 style="text-align: center; margin-bottom: 30px;">Meus Pedidos</h1>
        <table style="width: 100%; border-collapse: collapse; background-color: #f0d28b; border-radius: 8px; overflow: hidden; text-align: center;">
            <thead>
                <tr>
                    <th style="padding: 15px; background-color: #1c1c1c; color: #d4af37; width: 10%;">ID do Pedido</th>
                    <th style="padding: 15px; background-color: #1c1c1c; color: #d4af37; width: 15%;">Total</th>
                    <th style="padding: 15px; background-color: #1c1c1c; color: #d4af37; width: 20%;">Status Pedido</th>
                    <th style="padding: 15px; background-color: #1c1c1c; color: #d4af37; width: 20%;">Status Pagamento</th>
                    <th style="padding: 15px; background-color: #1c1c1c; color: #d4af37; width: 35%;">Data da Venda</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pedidos)): ?>
                    <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td data-label="ID do Pedido" style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;"><?php echo htmlspecialchars($pedido['id']); ?></td>
                        <td data-label="Total" style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">R$<?php echo htmlspecialchars($pedido['total']); ?></td>
                        <td data-label="Status Pedido" style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;"><?php echo htmlspecialchars($pedido['status_pedido']); ?></td>
                        <td data-label="Status Pagamento" style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;"><?php echo htmlspecialchars($pedido['status']); ?></td>
                        <td data-label="Data da Venda" style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;"><?php echo htmlspecialchars($pedido['data_venda']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 15px; text-align: center;">Você ainda não fez nenhum pedido.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
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

