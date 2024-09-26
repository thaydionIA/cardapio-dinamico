<?php
include('../controllers/KeyController.php');
$objKey = new KeyController();

// Verifica se a requisição veio da página do carrinho para finalizar a compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra'])) {
    // Configura a finalização da compra, como o ID do usuário e os dados necessários.
    // Você pode adicionar outras verificações se necessário.
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Finalizar Compra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">

    <style>
        /* Estilos personalizados para o formulário de pagamento */
        body {
            background-color: #1c1c1c; /* Fundo escuro */
            font-family: Arial, sans-serif; /* Fonte padrão */
            color: #d4af37; /* Texto dourado */
        }

        .container {
            background-color: #333; /* Fundo escuro para o container */
            padding: 30px; /* Espaçamento interno */
            border-radius: 10px; /* Bordas arredondadas */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra leve */
            max-width: 600px; /* Limita a largura do container */
            margin: 50px auto; /* Centraliza o container */
            color: #d4af37; /* Texto dourado */
        }

        /* Estilo dos campos de texto */
        input[type="text"] {
            width: 100%; /* Largura total do campo */
            padding: 10px; /* Espaço interno */
            margin-bottom: 15px; /* Espaço abaixo dos campos */
            border: 1px solid #444; /* Borda sutil */
            border-radius: 5px; /* Bordas arredondadas */
            background-color: #222; /* Fundo escuro para o campo */
            color: #d4af37; /* Texto dourado */
        }

        input[type="text"]::placeholder {
            color: #999; /* Cor para o texto do placeholder */
        }

        /* Estilo do botão */
        input[type="submit"] {
            background-color: #d4af37; /* Cor dourada */
            color: #1c1c1c; /* Texto escuro */
            border: none; /* Sem borda */
            padding: 12px 20px; /* Espaçamento interno */
            font-size: 16px; /* Tamanho da fonte */
            font-weight: bold; /* Negrito */
            border-radius: 5px; /* Bordas arredondadas */
            cursor: pointer; /* Muda o cursor para pointer */
            width: 100%; /* Botão ocupa toda a largura */
            transition: background-color 0.3s ease; /* Transição suave */
        }

        input[type="submit"]:hover {
            background-color: #ecbe54; /* Dourado mais claro no hover */
        }

        /* Centralizar o conteúdo */
        .col-6 {
            margin: 0 auto; /* Centraliza o conteúdo da coluna */
        }
    </style>

</head>
<body>
<form method="post" name="formCard" id="formCard" action="../controllers/PayController.php">
    <div class="container">
        <input type="hidden" name="finalizar_compra" value="1">
        <input type="text" name="publicKey" id="publicKey" value="<?php echo $objKey::getPublicKey(); ?>">
        <input type="text" name="encriptedCard" id="encriptedCard">
        <input type="text" class="form-control" name="cardNumber" id="cardNumber" maxlength="16" placeholder="Número do Cartão">
        <input type="text" class="form-control" name="cardHolder" id="cardHolder" placeholder="Nome no Cartão">
        <input type="text" class="form-control" name="cardMonth" id="cardMonth" maxlength="2" placeholder="Mês de Validade do Cartão">
        <input type="text" class="form-control" name="cardYear" id="cardYear" maxlength="4" placeholder="Ano do Cartão">
        <input type="text" class="form-control" name="cardCvv" id="cardCvv" maxlength="4" placeholder="CVV do Cartão">
        <input type="submit" class="btn btn-primary" value="Pagar">
    </div>
</form>
    <script src="https://assets.pagseguro.com.br/checkout-sdk-js/rc/dist/browser/pagseguro.min.js"></script>
    <script src="../assets/js/javascripts.js"></script>
</body>
</html>
