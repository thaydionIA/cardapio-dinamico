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

// Inicializa a contagem do carrinho se não estiver definida
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0; // Inicializa como 0 se não existir
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
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
        }
        .logo-container {
            display: flex;
            align-items: center;
        }
        .logo-container img {
            width: 50px;
            height: auto;
        }
        .site-title {
            font-size: 24px;
            color: #d4af37; /* Dourado */
            margin-left: 10px;
        }
        .right-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .search-container {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 20px;
            padding: 3px 10px;
            border: 1px solid #ccc;
        }
        .search-container input[type="text"] {
            padding: 5px;
            border: none;
            outline: none;
            width: 180px;
            border-radius: 20px;
        }
        .search-container button {
            background-color: transparent;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #d4af37; /* Ícone de busca em dourado */
        }
        .cart-icon {
            position: relative;
            cursor: pointer;
            color: #d4af37; /* Ícone do carrinho em dourado */
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
        nav {
            background-color: <?php echo $primary_color; ?>; /* Mantenha o fundo preto */
            padding: 10px 0; /* Adiciona um pouco de espaçamento vertical */
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        nav ul li a {
            text-decoration: none;
            color: #d4af37; /* Links do menu em dourado */
        }
        footer {
            background-color: <?php echo $primary_color; ?>;
        }
        .hidden {
            display: none !important; /* Isso garante que o contador não seja exibido */
        }
    </style>
</head>

<body>
    <header>
        <div class="logo-container">
            <img src="/cardapio-dinamico/path/logo.jpg" alt="Logo do Site">
            <h1 class="site-title"><?php echo $site_name; ?></h1>
        </div>

        <div class="right-icons">
            <!-- Ícone do carrinho de compras -->
            <div class="cart-icon" onclick="window.location.href='/cardapio-dinamico/carrinho.php'">
                <i class="fas fa-shopping-cart"></i>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span id="cart-count" class="cart-count <?php echo ($_SESSION['cart_count'] > 0) ? '' : 'hidden'; ?>">
                        <?php echo $_SESSION['cart_count']; ?>
                    </span>
                <?php else: ?>
                    <span id="cart-count" class="cart-count hidden"></span>
                <?php endif; ?>
            </div>

            <!-- Barra de busca ao lado do carrinho -->
            <div class="search-container">
                <form action="busca.php" method="GET">
                    <input type="text" name="q" placeholder="Buscar produtos..." required>
                    <button type="submit">
                        <i class="fas fa-search"></i> <!-- Ícone de busca (lupa) -->
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Menu dentro da área preta -->
    <nav>
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
                <li><a href="meus_pedidos.php">Meus Pedidos</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="banner">
        <img src="<?php echo $banner_image_path; ?>" alt="Banner" style="width:100%; height:auto;">
    </div>

    <main>
        <?php
        if (isset($_SESSION['user_id'])) {
            foreach ($sections as $id => $section) {
                if ($id === 'login' || $id === 'cadastro') continue;
                if (file_exists($section['url'])) include $section['url'];
            }
        } else {
            foreach ($sections as $id => $section) {
                if ($id === 'perfil') continue;
                if (file_exists($section['url'])) include $section['url'];
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
        if ($is_admin && file_exists('admin/logout.php')) {
            include 'admin/logout.php';
        }
        ?>
    </footer>

    <!-- Inclui o script.js se a página for acessada diretamente -->
    <script src="assets/js/script.js"></script>
</body>
</html>
