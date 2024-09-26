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
    <link rel="stylesheet" href="PIX.css">
</head>
<body>
    <div class="container">
        <h1>Pagamento com QR Code</h1>
        <?php $payController->createPayment(); ?>

        <!-- Status de pagamento com animação e cronômetro -->
        <p class="pix-status" id="pix-status">Aguardando pagamento via Pix...</p>
        <p>Tempo restante para o pagamento: <span id="timer">10:00</span></p> <!-- Cronômetro -->
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

        // Função para verificar o status do pagamento periodicamente
        function checkPaymentStatus(referenceId) {
            fetch('verificar_pagamento.php?reference_id=' + referenceId)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'pago') {
                        document.getElementById('pix-status').innerText = 'Pagamento Concluído';
                        document.getElementById('pix-status').style.color = 'green';
                        document.getElementById('pix-status').style.animation = 'none';
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000); // Redireciona após 2 segundos
                    }
                })
                .catch(error => console.error('Erro ao verificar pagamento:', error));
        }

        // Função para iniciar a contagem regressiva de 10 minutos
        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            var countdown = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                display.textContent = minutes + ':' + seconds;

                if (--timer < 0) {
                    clearInterval(countdown);
                    document.getElementById('pix-status').innerText = 'Pagamento Expirado';
                    document.getElementById('pix-status').style.color = 'red';
                    document.getElementById('pix-status').style.animation = 'none';
                }
            }, 1000);
        }

        // Inicia a contagem regressiva e a verificação do pagamento
        document.addEventListener('DOMContentLoaded', function () {
            var tenMinutes = 60 * 10, // 10 minutos em segundos
                display = document.querySelector('#timer'); // Seleciona o elemento do timer

            if (display) {
                startTimer(tenMinutes, display); // Inicia o cronômetro
            }

            // Recupera o referenceId do campo oculto
            var referenceIdElement = document.getElementById('reference-id');
            if (referenceIdElement) {
                var referenceId = referenceIdElement.value;
                setInterval(function() {
                    checkPaymentStatus(referenceId); // Verifica o status do pagamento a cada 5 segundos
                }, 5000);
            }
        });
    </script>
</body>
</html>
