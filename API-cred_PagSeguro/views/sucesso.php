<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Realizado com Sucesso</title>
    <link rel="stylesheet" href="/cardapio-dinamico/assets/css/style.css">
    <style>
        /* Estilo para o botão de voltar */
        .btn-voltar {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #d4af37; /* Cor dourada */
            color: #1c1c1c; /* Cor escura para o texto */
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-voltar:hover {
            background-color: #ecbe54; /* Cor mais clara ao passar o mouse */
        }

        .btn-container {
            text-align: center; /* Centralizar o botão */
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Pagamento Realizado com Sucesso!</h1>
    </header>

    <main>
        <div class="banner">
            <h2>Seu pagamento foi processado com sucesso.</h2>
            <p>Obrigado pela sua compra! Em breve você receberá um e-mail de confirmação com os detalhes da sua compra.</p>
        </div>

        <!-- Botão para voltar à página inicial -->
        <div class="btn-container">
            <a href="/cardapio-dinamico/index.php" class="btn-voltar">Voltar para a Página Inicial</a>
        </div>
    </main>

    <footer>
        <p>© 2024 Seu Site - Todos os direitos reservados.</p>
    </footer>
</body>
</html>
