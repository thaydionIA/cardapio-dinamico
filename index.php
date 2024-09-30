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
    /* Estilo para o contador do carrinho */
    .cart-icon {
        position: relative;
        display: inline-block;
    }
    .cart-count {
        position: absolute;
        top: -3px; /* Ajuste para baixar um pouco o contador */
        right: -2px; /* Ajuste para mover o contador um pouco mais para a direita */
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
        <div style="display: flex; align-items: center;">
            <img src="/cardapio-dinamico/path/logo.jpg" alt="Logo do Site" style="height: 60px; margin-right: 15px;">
            <h1><?php echo $site_name; ?></h1>
        </div>
        <nav>
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
            <ul>
                <?php foreach ($sections as $id => $section): ?>
                    <li><a href="<?php echo $section['url']; ?>"><?php echo $section['title']; ?></a></li>
                <?php endforeach; ?>
                
                <!-- Links para Login e Cadastro -->
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="cadastro.php">Cadastrar</a></li>
                <?php else: ?>
                    <li><a href="perfil.php">Meu Perfil</a></li>
                    <li><a href="meus_pedidos.php">Meu Pedidos</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="banner">
        <img src="<?php echo $banner_image_path; ?>" alt="Banner" style="width:100%; height:auto;">
    </div>

    <main>
    <?php
    // Verifica se o usuário está logado
    if (isset($_SESSION['user_id'])) {
        // Usuário logado - não mostra login ou cadastro
        foreach ($sections as $id => $section) {
            if ($id === 'login' || $id === 'cadastro') {
                continue; // Pula a inclusão de login e cadastro
            }

            if ($id === 'perfil') {
                continue; // Também pula a inclusão de perfil
            }

            // Inclui as demais seções
            if (file_exists($section['url'])) {
                include $section['url'];
            }
        }
    } else {
        // Usuário não está logado - inclui todas as seções exceto perfil
        foreach ($sections as $id => $section) {
            if ($id === 'perfil') {
                continue; // Pula a inclusão do perfil
            }

            if ($id === 'login' || $id === 'cadastro') {
                // Exibe login e cadastro apenas quando o usuário não está logado
                if (file_exists($section['url'])) {
                    include $section['url'];
                }
            } else {
                // Inclui todas as outras seções
                if (file_exists($section['url'])) {
                    include $section['url'];
                }
            }
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

    <!-- Inclui o arquivo de JavaScript centralizado -->
    <script src="assets/js/script.js"></script>
</body>
</html>
