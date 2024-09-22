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
</head>
<body>
<form method="post" name="formCard" id="formCard" action="../controllers/PayController.php">
    <div class="col-6 m-auto">
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
