<?php
$GLOBALS['incluir_rodape'] = false; // Define que o rodapé não deve ser incluído
include 'config.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio - <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            color: <?php echo $text_color; ?>;
        }
        header {
            background-color: <?php echo $primary_color; ?>;
        }
        footer {
            background-color: <?php echo $primary_color; ?>;
        }
    </style>
</head>

<body> 
    <header>  
        <div style="display: flex; align-items: center;">
            <img src="path/logo.jpg" alt="Logo do Site" style="height: 60px; margin-right: 15px;">
            <h1><?php echo $site_name; ?></h1>
        </div> 
        <nav>
            <ul>
                <?php foreach ($sections as $id => $section): ?>
                    <li><a href="<?php echo $section['url']; ?>"><?php echo $section['title']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>
     <!-- Adicione o código do banner aqui -->
     <div class="banner">
        <img src="<?php echo $banner_image_path; ?>" alt="Banner" style="width:100%; height:auto;">
    </div>

    <main>
        <?php 
        foreach ($sections as $id => $section) {
            include $section['url'];
        } 
        ?>
    </main>

    <footer>
        <p>&copy; 2024 <?php echo $site_name; ?>. Todos os direitos reservados.</p>
        <li><a href="admin/login.php">Painel Administrativo</a></li>
    </footer>
</body>
</html>
