<?php
session_start(); // Inicia a sessão para acessar o user_id
require_once('../config/conexao.php'); // Configuração do banco de dados
require_once ('ProductController.php'); // Controlador de produtos
require_once 'UserController.php'; // Controlador de usuários
require_once 'AddressController.php'; // Controlador de endereços

class PayController {
    private $pdo;
    private $productController;
    
    private $userController;
    private $addressController;

    public function __construct($pdo, $productController, $userController, $addressController) {
        $this->pdo = $pdo; // Recebe o PDO
        $this->productController = $productController;
        $this->userController = $userController;
        $this->addressController = $addressController;
    }

    public function createPayment() {
        // Verifica se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            echo "Usuário não logado.";
            header('Location: login.php'); // Redireciona para a página de login se o usuário não estiver logado
            exit();
        }

        // Obtém o ID do usuário da sessão
        $userId = $_SESSION['user_id'];

        // Busca o usuário no banco de dados
        $user = $this->userController->getUserById($userId);
        if (!$user) {
            echo "Usuário não encontrado.";
            return;
        }

        // Busca o endereço do usuário no banco de dados
        $address = $this->addressController->getAddressByUserId($userId);
        if (!$address) {
            echo "Endereço não encontrado.";
            return;
        }

        // Limpa e valida o CEP e o telefone
        $cepLimpo = preg_replace('/[^0-9]/', '', $address['cep']); // Remove caracteres não numéricos do CEP
        $telefoneLimpo = preg_replace('/[^0-9]/', '', $user['telefone']); // Remove caracteres não numéricos do telefone

        // Verifica se o telefone tem entre 8 a 9 dígitos
        if (strlen($telefoneLimpo) < 8 || strlen($telefoneLimpo) > 9) {
            echo "Número de telefone inválido. Deve ter entre 8 e 9 dígitos.";
            exit();
        }

        // Busca os itens do carrinho do usuário
        $stmt = $this->pdo->prepare("
            SELECT p.id, p.nome, p.preco, c.quantidade 
            FROM carrinho c
            JOIN produtos p ON c.produto_id = p.id
            WHERE c.usuario_id = :usuario_id
        ");
        $stmt->execute(['usuario_id' => $userId]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verifica se o carrinho está vazio
        if (empty($itens)) {
            echo "Carrinho vazio. Adicione produtos antes de realizar o pagamento.";
            header('Location: carrinho.php');
            exit();
        }

        // Configura os dados do pagamento com base nos dados do produto, usuário e endereço
        $data = [
            'reference_id' => "pedido-" . uniqid(), // Gera uma referência única para o pedido
            'customer' => [
                'name' => $user['nome'], // Nome do usuário
                'email' => $user['email'], // E-mail do usuário
                'tax_id' => preg_replace('/[^0-9]/', '', $user['cpf']), // CPF do usuário sem máscaras
                'phones' => [
                    [
                        'country' => '55',
                        'area' => $user['dd'],
                        'number' => $telefoneLimpo, // Telefone limpo e ajustado
                        'type' => 'MOBILE'
                    ]
                ]
            ],
            'items' => [], // Lista de itens a ser preenchida com os produtos do carrinho
            'shipping' => [
                'address' => [
                    'street' => $address['rua'],
                    'number' => $address['numero'],
                    'complement' => $address['complemento'],
                    'locality' => $address['bairro'],
                    'city' => $address['cidade'],
                    'region_code' => $address['estado'],
                    'country' => $address['pais'],
                    'postal_code' => $cepLimpo // CEP limpo e ajustado
                ]
            ],
            'notification_urls' => [
                'https://webhook.site/7e9a29f1-3ffe-4c38-a30e-eb8e4e373d67'
            ],
            'charges' => [
                [
                    'reference_id' => "cobranca-" . uniqid(),
                    'description' => 'Cobrança dos produtos do carrinho',
                    'amount' => [
                        'value' => 0, // Será atualizado conforme os itens do carrinho
                        'currency' => 'BRL'
                    ],
                    'payment_method' => [
                        'type' => 'CREDIT_CARD',
                        'installments' => 1,
                        'capture' => true,
                        'card' => [
                            'encrypted' => $_POST['encriptedCard'], // Certifique-se que este valor está sendo enviado do frontend
                            'security_code' => '123', // Substitua pelo código real
                            'holder' => [
                                'name' => $user['nome'] // Nome do titular do cartão, capturado dos dados do usuário
                            ],
                            'store' => true
                        ]
                    ]
                ]
            ]
        ];

        // Adiciona os produtos do carrinho aos itens do pagamento e atualiza o valor total
        $valorTotal = 0;
        foreach ($itens as $item) {
            $data['items'][] = [
                'reference_id' => "item-{$item['id']}",
                'name' => $item['nome'],
                'quantity' => $item['quantidade'],
                'unit_amount' => intval($item['preco'] * 100) // Convertendo para centavos
            ];
            $valorTotal += $item['preco'] * $item['quantidade'] * 100; // Totalizando o valor em centavos
        }
        // Atualiza o valor total da cobrança
        $data['charges'][0]['amount']['value'] = intval($valorTotal);

        // Inicializa o cURL para enviar a solicitação de pagamento ao PagSeguro
        $curl = curl_init('https://sandbox.api.pagseguro.com/orders');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: 279c9a74-9859-45cf-a74b-c336795236940fcf1fe54a438803fd2c2d6d9e80b69b3122-2b98-4396-a7b9-23aed7fc468c' // Substitua pela chave de produção em ambiente real
        ]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($curl);
        curl_close($curl);

        // Decodifica a resposta da API para verificar o status
        $responseData = json_decode($response, true);

        // Verifica se o pagamento foi realizado com sucesso
        if (isset($responseData['charges'][0]['status']) && $responseData['charges'][0]['status'] === 'PAID') {
            // Redireciona para a página de sucesso
            header('Location: http://localhost/cardapio-dinamico/API-cred_PagSeguro/views/sucesso.php');    
            exit();
        } else {
            // Redireciona para a página de falha
            header('Location: http://localhost/cardapio-dinamico/API-cred_PagSeguro/views/falha.php');
            exit();
        }
    }
}

// Exemplo de uso
$productController = new ProductController($pdo);
$userController = new UserController($pdo);
$addressController = new AddressController($pdo);
$payController = new PayController($pdo, $productController, $userController, $addressController);

// Chamando o método sem especificar um produto, pois agora ele utiliza o carrinho do usuário
$payController->createPayment();
?>
<?php
session_start(); // Inicia a sessão para acessar o user_id
require_once('../config/conexao.php'); // Configuração do banco de dados
require_once ('ProductController.php'); // Controlador de produtos
require_once 'UserController.php'; // Controlador de usuários
require_once 'AddressController.php'; // Controlador de endereços

class PayController {
    private $pdo;
    private $productController;
    
    private $userController;
    private $addressController;

    public function __construct($pdo, $productController, $userController, $addressController) {
        $this->pdo = $pdo; // Recebe o PDO
        $this->productController = $productController;
        $this->userController = $userController;
        $this->addressController = $addressController;
    }

    public function createPayment() {
        // Verifica se o usuário está logado
        if (!isset($_SESSION['user_id'])) {
            echo "Usuário não logado.";
            header('Location: login.php'); // Redireciona para a página de login se o usuário não estiver logado
            exit();
        }

        // Obtém o ID do usuário da sessão
        $userId = $_SESSION['user_id'];

        // Busca o usuário no banco de dados
        $user = $this->userController->getUserById($userId);
        if (!$user) {
            echo "Usuário não encontrado.";
            return;
        }

        // Busca o endereço do usuário no banco de dados
        $address = $this->addressController->getAddressByUserId($userId);
        if (!$address) {
            echo "Endereço não encontrado.";
            return;
        }

        // Limpa e valida o CEP e o telefone
        $cepLimpo = preg_replace('/[^0-9]/', '', $address['cep']); // Remove caracteres não numéricos do CEP
        $telefoneLimpo = preg_replace('/[^0-9]/', '', $user['telefone']); // Remove caracteres não numéricos do telefone

        // Verifica se o telefone tem entre 8 a 9 dígitos
        if (strlen($telefoneLimpo) < 8 || strlen($telefoneLimpo) > 9) {
            echo "Número de telefone inválido. Deve ter entre 8 e 9 dígitos.";
            exit();
        }

        // Busca os itens do carrinho do usuário
        $stmt = $this->pdo->prepare("
            SELECT p.id, p.nome, p.preco, c.quantidade 
            FROM carrinho c
            JOIN produtos p ON c.produto_id = p.id
            WHERE c.usuario_id = :usuario_id
        ");
        $stmt->execute(['usuario_id' => $userId]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verifica se o carrinho está vazio
        if (empty($itens)) {
            echo "Carrinho vazio. Adicione produtos antes de realizar o pagamento.";
            header('Location: carrinho.php');
            exit();
        }

        // Configura os dados do pagamento com base nos dados do produto, usuário e endereço
        $data = [
            'reference_id' => "pedido-" . uniqid(), // Gera uma referência única para o pedido
            'customer' => [
                'name' => $user['nome'], // Nome do usuário
                'email' => $user['email'], // E-mail do usuário
                'tax_id' => preg_replace('/[^0-9]/', '', $user['cpf']), // CPF do usuário sem máscaras
                'phones' => [
                    [
                        'country' => '55',
                        'area' => $user['dd'],
                        'number' => $telefoneLimpo, // Telefone limpo e ajustado
                        'type' => 'MOBILE'
                    ]
                ]
            ],
            'items' => [], // Lista de itens a ser preenchida com os produtos do carrinho
            'shipping' => [
                'address' => [
                    'street' => $address['rua'],
                    'number' => $address['numero'],
                    'complement' => $address['complemento'],
                    'locality' => $address['bairro'],
                    'city' => $address['cidade'],
                    'region_code' => $address['estado'],
                    'country' => $address['pais'],
                    'postal_code' => $cepLimpo // CEP limpo e ajustado
                ]
            ],
            'notification_urls' => [
                'https://webhook.site/7e9a29f1-3ffe-4c38-a30e-eb8e4e373d67'
            ],
            'charges' => [
                [
                    'reference_id' => "cobranca-" . uniqid(),
                    'description' => 'Cobrança dos produtos do carrinho',
                    'amount' => [
                        'value' => 0, // Será atualizado conforme os itens do carrinho
                        'currency' => 'BRL'
                    ],
                    'payment_method' => [
                        'type' => 'CREDIT_CARD',
                        'installments' => 1,
                        'capture' => true,
                        'card' => [
                            'encrypted' => $_POST['encriptedCard'], // Certifique-se que este valor está sendo enviado do frontend
                            'security_code' => '123', // Substitua pelo código real
                            'holder' => [
                                'name' => $user['nome'] // Nome do titular do cartão, capturado dos dados do usuário
                            ],
                            'store' => true
                        ]
                    ]
                ]
            ]
        ];

        // Adiciona os produtos do carrinho aos itens do pagamento e atualiza o valor total
        $valorTotal = 0;
        foreach ($itens as $item) {
            $data['items'][] = [
                'reference_id' => "item-{$item['id']}",
                'name' => $item['nome'],
                'quantity' => $item['quantidade'],
                'unit_amount' => intval($item['preco'] * 100) // Convertendo para centavos
            ];
            $valorTotal += $item['preco'] * $item['quantidade'] * 100; // Totalizando o valor em centavos
        }
        // Atualiza o valor total da cobrança
        $data['charges'][0]['amount']['value'] = intval($valorTotal);

        // Inicializa o cURL para enviar a solicitação de pagamento ao PagSeguro
        $curl = curl_init('https://sandbox.api.pagseguro.com/orders');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: 279c9a74-9859-45cf-a74b-c336795236940fcf1fe54a438803fd2c2d6d9e80b69b3122-2b98-4396-a7b9-23aed7fc468c' // Substitua pela chave de produção em ambiente real
        ]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($curl);
        curl_close($curl);

        // Decodifica a resposta da API para verificar o status
        $responseData = json_decode($response, true);

        // Verifica se o pagamento foi realizado com sucesso
        if (isset($responseData['charges'][0]['status']) && $responseData['charges'][0]['status'] === 'PAID') {
            // Redireciona para a página de sucesso
            header('Location: http://localhost/cardapio-dinamico/API-cred_PagSeguro/views/sucesso.php');    
            exit();
        } else {
            // Redireciona para a página de falha
            header('Location: http://localhost/cardapio-dinamico/API-cred_PagSeguro/views/falha.php');
            exit();
        }
    }
}

// Exemplo de uso
$productController = new ProductController($pdo);
$userController = new UserController($pdo);
$addressController = new AddressController($pdo);
$payController = new PayController($pdo, $productController, $userController, $addressController);

// Chamando o método sem especificar um produto, pois agora ele utiliza o carrinho do usuário
$payController->createPayment();
?>
