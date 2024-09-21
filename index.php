<?php
session_start(); // Inicia a sessão
$GLOBALS['incluir_rodape'] = false; // Define que o rodapé não deve ser incluído

// Verifica se o config.php existe antes de incluí-lo
if (file_exists('config.php')) {
    include 'config.php';
} else {
    echo 'Arquivo config.php não encontrado.';
    exit; // Sai do script se o arquivo config.php não for encontrado
}

// Verifique se o usuário está na parte de admin
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio - <?php echo $site_name; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <!-- Ajustado o caminho da logo para um caminho absoluto -->
            <img src="path/logo.jpg" alt="Logo do Site" style="height: 60px; margin-right: 15px;">
            <h1><?php echo $site_name; ?></h1>
        </div>
        <nav>
            <div class="cart-icon" onclick="window.location.href='/cardapio-dinamico/carrinho.php'">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <ul>
                <?php foreach ($sections as $id => $section): ?>
                    <li><a href="<?php echo $section['url']; ?>"><?php echo $section['title']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>

    <div class="banner">
        <img src="<?php echo $banner_image_path; ?>" alt="Banner" style="width:100%; height:auto;">
    </div>

    <main>
    <?php 
    // Inclui as seções, mas ignora login e cadastro se o usuário não estiver logado
    foreach ($sections as $id => $section) {
        if (!isset($_SESSION['user_id']) && ($id === 'login' || $id === 'cadastro')) {
            continue; 
        }
        
        if ($id === 'perfil' && isset($_SESSION['user_id'])) {
            continue; // Não inclui a seção de perfil se o usuário estiver logado
        }

        // Verifica se o arquivo da seção existe antes de incluí-lo, sem mostrar mensagem
        if (file_exists($section['url'])) {
            include $section['url'];
        }
    }
    ?>
    </main>

    <footer>
        <p>&copy; 2024 <?php echo $site_name; ?>. Todos os direitos reservados.</p>
        <ul>
            <li><a href="admin/login.php">Painel Administrativo</a></li>
        </ul>
        <?php 
        // Inclui logout.php apenas na parte administrativa
        if ($is_admin && file_exists('admin/logout.php')) {
            include 'admin/logout.php';
        }
        ?>
    </footer>

    <!-- Código JavaScript -->
    <script>
        function registrarEventosQuantidade() {
            document.querySelectorAll('.aumentar').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentNode.querySelector('.quantidade-input');
                    input.value = parseInt(input.value) + 1;
                });
            });

            document.querySelectorAll('.diminuir').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentNode.querySelector('.quantidade-input');
                    if (parseInt(input.value) > 1) {
                        input.value = parseInt(input.value) - 1;
                    }
                });
            });
        }

        function removerEventosQuantidade() {
            document.querySelectorAll('.aumentar').forEach(button => {
                const clone = button.cloneNode(true);
                button.parentNode.replaceChild(clone, button);
            });

            document.querySelectorAll('.diminuir').forEach(button => {
                const clone = button.cloneNode(true);
                button.parentNode.replaceChild(clone, button);
            });
        }

        function initQuantidade() {
            removerEventosQuantidade();
            registrarEventosQuantidade();
        }

        document.addEventListener('DOMContentLoaded', function() {
            initQuantidade();
        });
    </script>
</body>
</html>