<?php
include 'config.php';

// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia a sessão se ainda não estiver ativa
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio - <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="/cardapio-dinamico/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            color: <?php echo $text_color; ?>; 
        }
        header {
            background-color: <?php echo $primary_color; ?>;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
        }
        .logo-container {
            display: inline-block;
            width: 100px;
            height: 100px;
            overflow: hidden;
        }
        .logo-container img {
            width: 100%;
            height: auto;
        }
        .return-button-container {
            display: inline-block;
        }
        .return-button {
            text-decoration: none;
            color: white;
            background-color: #f0d28b;
            padding: 10px 20px;
            border-radius: 5px;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 15px;
        }
        nav ul li {
            display: inline;
        }
        nav ul li a {
            text-decoration: none;
            color: white;
        }
        .cart-icon {
            position: relative;
            cursor: pointer;
            display: inline-block;
        }
        .cart-count {
            position: absolute;
            top: -3px;
            right: -2px;
            transform: translate(50%, -50%);
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 0;
            width: 16px;
            height: 16px;
            font-size: 10px;
            text-align: center;
            line-height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hidden {
            display: none !important; /* Garante que o contador fique oculto */
        }
    </style>
</head>
<body>

<header>
    <div class="logo-container">
        <img src="/cardapio-dinamico/path/logo.jpg" alt="Logo do Cliente" class="logo">
    </div>

    <div class="return-button-container">
        <a href="/cardapio-dinamico/index.php" class="return-button">Retornar ao Início</a>
    </div>

    <!-- Ícone do carrinho de compras -->
    <div class="cart-icon" onclick="window.location.href='/cardapio-dinamico/carrinho.php'">
        <i class="fas fa-shopping-cart"></i>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Inicia sem a classe 'hidden' para deixar o JS controlar a visibilidade -->
            <span id="cart-count" class="cart-count">0</span>
        <?php else: ?>
            <!-- Mostra o ícone sem o contador se o usuário não estiver logado -->
            <span id="cart-count" class="cart-count hidden"></span>
        <?php endif; ?>
    </div>

    <nav>
        <ul>
            <?php foreach ($sections as $id => $section): ?>
                <li><a href="/cardapio-dinamico/<?php echo $section['url']; ?>"><?php echo $section['title']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>

<main>

<!-- Inclua o arquivo JavaScript -->
<script src="/cardapio-dinamico/assets/js/scripts.js"></script>

</body>
</html>
