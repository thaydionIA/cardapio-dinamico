<?php include 'config.php'; ?>
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
           width: 100px; /* Ajuste a largura desejada */
           height: 100px; /* Ajuste a altura desejada */
            overflow: hidden; /* Isso impede que o conteúdo exceda o tamanho do container */
        }
        .logo-container img {
           width: 100%;
           height: auto; /* Mantém a proporção da imagem ao ajustar a largura */
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
    </style>
</head>
<body>

<header>
    <!-- Espaço para a logo do cliente -->
    <div class="logo-container">
        <img src="../path/logo.jpg" alt="Logo do Cliente" class="logo">
    </div>

    <!-- Botão para retornar ao index principal -->
    <div class="return-button-container">
        <a href="/cardapio-dinamico/index.php" class="return-button">Retornar ao Início</a>
    </div>
    <div class="cart-icon" onclick="window.location.href='/cardapio-dinamico/carrinho.php'">
    <i class="fas fa-shopping-cart"></i>
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
