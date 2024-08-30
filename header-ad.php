<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio - <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="/cardapio-dinamico/assets/css/style.css">
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
    </style>
</head>
<body>

<header>
    <div class="logo-container">
        <img src="../path/logo.jpg" alt="Logo do Cliente" class="logo">
        <h1><?php echo $site_name; ?></h1>
    </div>
    <!-- Botão para retornar ao index principal -->
    <div class="return-button-container">
        <a href="/cardapio-dinamico/index.php" class="return-button">Retornar ao Início</a>
    </div>

</header>

<main>
