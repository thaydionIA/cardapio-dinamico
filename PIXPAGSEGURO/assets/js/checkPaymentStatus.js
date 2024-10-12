function copyPixKey() {
    var pixKeyInput = document.getElementById('pixKeyInput').value;
    navigator.clipboard.writeText(pixKeyInput).then(function() {
        alert('Chave Pix copiada com sucesso!');
    }).catch(function(error) {
        alert('Erro ao copiar a chave Pix: ' + error);
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
                setTimeout(function() {
                    window.location.href = 'index.php'; // Redireciona para a página do site
                }, 2000); 
            } else {
                document.getElementById('pix-status').innerText = 'Aguardando pagamento...';
            }
        })
        .catch(error => console.error('Erro ao verificar pagamento:', error));
}

document.addEventListener('DOMContentLoaded', function () {
    var referenceId = document.getElementById('referenceId').value;
    setInterval(function() {
        checkPaymentStatus(referenceId); // Verifica o status do pagamento a cada 5 segundos
    }, 5000);
});
