<?php
session_start();
require_once('../config/conexao.php');
require_once 'ProductControllerPix.php'; 
require_once 'userControllerPix.php'; 
require_once 'AddressControllerPix.php'; 

// Ativando a exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class PayController {
    private $pdo;
    private $productcontrollerpix;
    private $usercontrollerpix;
    private $addressControllerpix;

    public function __construct($pdo, $productcontrollerpix, $usercontrollerpix, $addressControllerpix) {
        $this->pdo = $pdo;
        $this->productcontrollerpix = $productcontrollerpix;
        $this->usercontrollerpix = $usercontrollerpix;
        $this->addressControllerpix = $addressControllerpix;
    }

    public function createPayment() {
        // Captura o user_id da sessão
        if (!isset($_SESSION['user_id'])) {
            echo "Erro: Usuário não está logado.";
            return;
        }

        $userId = $_SESSION['user_id'];

        // Busca os itens do carrinho do usuário
        $stmt = $this->pdo->prepare("SELECT c.quantidade, p.* 
                                     FROM carrinho c
                                     JOIN produtos p ON c.produto_id = p.id
                                     WHERE c.usuario_id = :usuario_id");
        $stmt->execute(['usuario_id' => $userId]);
        $carrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($carrinho)) {
            echo "Carrinho vazio. Adicione produtos antes de finalizar a compra.";
            return;
        }

        // Calcula o valor total da compra e prepara os itens para o pagamento
        $total = 0;
        $items = [];
        foreach ($carrinho as $item) {
            $total += $item['preco'] * $item['quantidade'];
            $items[] = [
                'reference_id' => "item-{$item['id']}",
                'name' => $item['nome'],
                'quantity' => $item['quantidade'],
                'unit_amount' => intval($item['preco'] * 100), // Convertendo para centavos
            ];
        }

        // Busca o usuário no banco de dados
        $user = $this->usercontrollerpix->getUserById($userId);
        if (!$user) {
            echo "Usuário não encontrado.";
            return;
        }

        // Busca o endereço do usuário
        $address = $this->addressControllerpix->getAddressByUserId($userId);
        if (!$address) {
            echo "Endereço não encontrado.";
            return;
        }

        // Define o token e a URL (produção ou sandbox)
        define('TOKEN', '279c9a74-9859-45cf-a74b-c336795236940fcf1fe54a438803fd2c2d6d9e80b69b3122-2b98-4396-a7b9-23aed7fc468c');
        define('URL', 'https://sandbox.api.pagseguro.com/orders');

        // Define os campos a serem enviados ao PagSeguro
        $data = [
            'reference_id' => "pedido-{$userId}-" . time(),
            'customer' => [
                'name' => $user['nome'], 
                'email' => $user['email'], 
                'tax_id' => $user['cpf'], 
                'phones' => [
                    [
                        'country' => '55',
                        'area' => $user['dd'], 
                        'number' => $user['telefone'], 
                        'type' => 'MOBILE'
                    ]
                ]
            ],
            'items' => $items,
            'qr_codes' => [
                [
                    'amount' => [
                        'value' => intval($total * 100) // Total convertido para centavos
                    ],
                    'expiration' => '2023-03-29T20:15:59-03:00'
                ]
            ],
            'shipping' => [
                'address' => [
                    'street' => $address['rua'],
                    'number' => $address['numero'],
                    'complement' => $address['complemento'],
                    'locality' => $address['bairro'],
                    'city' => $address['cidade'],
                    'region_code' => $address['estado'],
                    'country' => $address['pais'],
                    'postal_code' => $address['cep']
                ]
            ],
            'notification_urls' => [
                'https://meusite.com/notificacoes'
            ]
        ];

        $data = json_encode($data);

        // Requisição de pagamento via cURL
        $curl = curl_init(URL);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . TOKEN,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        curl_close($curl);

        // Decodifica a resposta JSON
        $response = json_decode($response);

        // Verifica se o QR Code e a chave Pix foram gerados corretamente
        if (isset($response->qr_codes[0]->links[0]->href) && isset($response->qr_codes[0]->text)) {
            $qrCodeUrl = $response->qr_codes[0]->links[0]->href;
            $pixKey = $response->qr_codes[0]->text;

            // Exibe o QR Code e a chave Pix
            echo "<div id='qrcode'>";
            echo "<img src='" . $qrCodeUrl . "' alt='Qrcode Pix'>";
            echo "</div>";
            echo "<p>Chave Pix:</p>";
            echo "<div class='pix-key-box'>";
            echo "<input type='text' id='pixKeyInput' value='" . $pixKey . "' readonly class='pix-key-input'>";
            echo "<button class='copy-btn' onclick='copyPixKey()'>Copiar</button>";
            echo "</div>";
        } else {
            echo "<p>Erro ao gerar o QR Code e chave Pix.</p>";
        }
    }
}

// Exemplo de uso
$productcontrollerpix = new ProductControllerPix($pdo);
$usercontrollerpix = new UserControllerPix($pdo);
$addressControllerpix = new AddressControllerPix($pdo);

// Instanciando o PayController
$payController = new PayController($pdo, $productcontrollerpix, $usercontrollerpix, $addressControllerpix);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento com QR Code</title>
    <style>
        /* Estilo geral da página */
        body {
            background-color: #1c1c1c; /* Fundo escuro */
            font-family: Arial, sans-serif;
            color: #d4af37; /* Texto dourado */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Container para o conteúdo */
        .container {
            background-color: #333; /* Fundo escuro para o container */
            padding: 30px; /* Espaçamento interno */
            border-radius: 10px; /* Bordas arredondadas */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra suave */
            text-align: center; /* Centralizar o conteúdo */
            max-width: 600px; /* Largura máxima do container */
            margin: auto;
        }

        /* Estilo do título */
        h1 {
            color: #d4af37; /* Cor dourada para o título */
            margin-bottom: 20px;
            font-size: 24px;
        }

        /* Estilo da imagem do QR Code */
        #qrcode img {
            width: 250px; /* Tamanho fixo do QR Code */
            height: 250px;
            margin: 20px 0;
            border-radius: 10px; /* Bordas arredondadas para a imagem */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra na imagem */
        }

        /* Caixa de texto para a chave Pix */
        .pix-key-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .pix-key-input {
            background-color: #222; /* Fundo escuro para a caixa de texto */
            color: #d4af37; /* Texto dourado */
            border: 1px solid #444; /* Borda sutil */
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            margin-right: 10px;
        }

        /* Botão de copiar a chave Pix */
        .copy-btn {
            background-color: #d4af37; /* Fundo dourado */
            color: #1c1c1c; /* Texto escuro */
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .copy-btn:hover {
            background-color: #ecbe54; /* Cor mais clara ao passar o mouse */
        }

        /* Link para continuar após o pagamento */
        .continue-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #d4af37; /* Fundo dourado */
            color: #1c1c1c; /* Texto escuro */
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .continue-link:hover {
            background-color: #ecbe54; /* Dourado mais claro no hover */
        }

        /* Mensagem de erro ao gerar o QR Code */
        p.error-message {
            color: #ff4d4d; /* Cor vermelha para mensagem de erro */
            font-size: 18px;
        }

        /* Responsividade para telas menores */
        @media (max-width: 768px) {
            .container {
                padding: 20px; /* Reduz o espaçamento para telas menores */
                width: 90%;
            }

            #qrcode img {
                width: 200px; /* Ajusta o tamanho do QR Code em telas menores */
                height: 200px;
            }

            .pix-key-input {
                font-size: 14px; /* Ajusta o tamanho da fonte da chave Pix */
            }

            .copy-btn {
                font-size: 14px; /* Ajusta o tamanho do botão em telas menores */
                padding: 8px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pagamento com QR Code</h1>
        <?php $payController->createPayment(); ?>
    </div>

    <script>
        function copyPixKey() {
            // Pega o valor da chave Pix
            var pixKeyInput = document.getElementById('pixKeyInput').value;
            
            // Usa a API Clipboard para copiar o texto para a área de transferência
            navigator.clipboard.writeText(pixKeyInput).then(function() {
                alert('Chave Pix copiada com sucesso!');
            }).catch(function(error) {
                alert('Falha ao copiar a chave Pix: ' + error);
            });
        }
    </script>
</body>
</html>
