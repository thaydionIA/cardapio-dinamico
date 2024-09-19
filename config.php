<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
} // Inicia a sessão para gerenciar o estado do usuário

// Configurações básicas
$site_name = "Reservado (Nome do Estabelecimento)";
$primary_color = "#1c1c1c";
$secondary_color = "#f4f4f4";
$text_color = "#d4af37";
$banner_image_path = '../cardapio-dinamico/path/r.jpg'; // Substitua pelo caminho correto do banner

// Definição de seções com caminhos corretos
$sections = [
    "entradas" => [
        "title" => "Entradas",
        "url" => "sections/entradas.php"
    ], 
    "principais" => [
        "title" => "Pratos Principais",
        "url" => "sections/principais.php"
    ],
    "bebidas" => [
        "title" => "Bebidas",
        "url" => "sections/bebidas.php"
    ],
    "sobremesas" => [
        "title" => "Sobremesas",
        "url" => "sections/sobremesas.php"
    ],
];

// Verifica se o usuário está logado e adiciona o link de "Meu Perfil"
if (isset($_SESSION['user_id'])) {
    // Adiciona "Meu Perfil" ao menu
    $sections['perfil'] = [
        "title" => "Meu Perfil",
        "url" => "perfil.php"
    ];
    // Adiciona a opção de Sair
    $sections['logout'] = [
        "title" => "Sair",
        "url" => "logout.php"
    ];
} else {
    // Se o usuário não estiver logado, exibe as opções de Login e Cadastro
    $sections['login'] = [
        "title" => "Login",
        "url" => "login.php"
    ];
    $sections['cadastro'] = [
        "title" => "Cadastrar",
        "url" => "cadastro.php"
    ];
}
?>

<!-- Menu dinâmico gerado com base nas seções -->
<nav style="background-color: <?= $primary_color ?>; color: <?= $text_color ?>;">
    <ul>
        <?php foreach ($sections as $key => $section): ?>
            <li><a href="<?= $section['url'] ?>" style="color: <?= $text_color ?>;"><?= $section['title'] ?></a></li>
        <?php endforeach; ?>
    </ul>
</nav>
