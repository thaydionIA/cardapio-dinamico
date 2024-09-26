// Função para remover todos os listeners existentes
function limparListeners() {
    document.querySelectorAll('.aumentar, .diminuir, .adicionar-carrinho-btn').forEach(button => {
        const newButton = button.cloneNode(true); // Cria um clone sem event listeners
        button.parentNode.replaceChild(newButton, button); // Substitui o botão original pelo clone
    });
}

// Função para adicionar event listeners
function adicionarListeners() {
    limparListeners(); // Primeiro, limpa todos os event listeners existentes

    // Adiciona eventos aos botões de aumentar e diminuir quantidade
    document.querySelectorAll('.aumentar').forEach(button => {
        button.addEventListener('click', function () {
            const input = this.parentNode.querySelector('.quantidade-input');
            input.value = parseInt(input.value) + 1;
        });
    });

    document.querySelectorAll('.diminuir').forEach(button => {
        button.addEventListener('click', function () {
            const input = this.parentNode.querySelector('.quantidade-input');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        });
    });

    // Adiciona eventos aos botões de adicionar ao carrinho
    document.querySelectorAll('.adicionar-carrinho-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Impede o envio padrão do formulário
            const form = this.closest('.adicionar-carrinho-form');
            const produtoId = form.querySelector('input[name="produto_id"]').value;
            const quantidade = form.querySelector('input[name="quantidade"]').value;

            fetch('/cardapio-dinamico/carrinho.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    produto_id: produtoId,
                    quantidade: quantidade
                })
            })
            .then(response => response.text())
            .then(data => {
                const mensagem = document.getElementById('mensagemSucesso');
                mensagem.classList.add('mostrar');
                setTimeout(() => {
                    mensagem.classList.remove('mostrar');
                }, 3000); // A mensagem será exibida por 3 segundos
            })
            .catch(error => {
                console.error('Erro ao adicionar ao carrinho:', error);
                alert('Ocorreu um erro ao adicionar o produto ao carrinho.');
            });
        });
    });
}

// Chama a função para adicionar event listeners após o DOM ser carregado
document.addEventListener('DOMContentLoaded', () => {
    adicionarListeners();
});
