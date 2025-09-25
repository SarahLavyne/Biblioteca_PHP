// Espera todo o conteúdo do HTML ser carregado antes de executar o JavaScript.
// Isso evita erros de "elemento não encontrado" (null).
document.addEventListener('DOMContentLoaded', () => {

    // --- SEÇÃO DE CADASTRO DE LIVROS ---

    // 1. Pegar referências dos elementos HTML do formulário
    const formCadastro = document.getElementById('form-cadastro');
    const mensagemStatus = document.getElementById('mensagem-status');

    // 2. Adicionar um "escutador" para o evento de 'submit' (envio) do formulário
    //    É importante verificar se 'formCadastro' não é nulo, caso o ID esteja errado no HTML
    if (formCadastro) {
        formCadastro.addEventListener('submit', (event) => {
            // Previne o comportamento padrão do formulário, que é recarregar a página
            event.preventDefault();

            // Cria um objeto com os dados do formulário para enviar na requisição
            const formData = new FormData(formCadastro);

            // Envia os dados para a API PHP usando fetch com o método POST
            fetch('../api/cadastrar_livro.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Converte a resposta do PHP em um objeto JSON
            .then(data => {
                // Exibe a mensagem de resposta (sucesso ou erro) vinda do PHP
                mensagemStatus.textContent = data.mensagem;
                
                if (data.sucesso) {
                    mensagemStatus.className = 'sucesso'; // Aplica a classe CSS de sucesso
                    formCadastro.reset(); // Limpa os campos do formulário
                    
                    // ATUALIZA A LISTA! Chama a função para recarregar os livros e mostrar o novo
                    carregarLivros(); 
                } else {
                    mensagemStatus.className = 'erro'; // Aplica a classe CSS de erro
                }
            })
            .catch(error => {
                // Executa se houver um erro de rede ou de comunicação com a API
                console.error('Erro na requisição de cadastro:', error);
                mensagemStatus.textContent = 'Ocorreu um erro de comunicação ao tentar cadastrar.';
                mensagemStatus.className = 'erro';
            });
        });
    } else {
        console.error("Erro: Elemento com id 'form-cadastro' não foi encontrado no HTML.");
    }

    // --- SEÇÃO DE LISTAGEM DE LIVROS ---

    // 1. Pegar referência do container onde a lista de livros será exibida
    const listaLivrosDiv = document.getElementById('lista-livros');

    if(listaLivrosDiv) {
        listaLivrosDiv.addEventListener('click', (event) => {
            // Verifica se o elemento clicado foi um botão com a classe 'btn-deletar'
            if (event.target.classList.contains('btn-deletar')) {
                
                // Pede confirmação ao usuário antes de prosseguir
                const confirmar = confirm('Tem certeza que deseja deletar este livro? Esta ação é irreversível.');

                if (confirmar) {
                    const livroId = event.target.dataset.id; // Pega o ID do atributo 'data-id' do botão
                    deletarLivro(livroId);
                }
            }
        });
    }

    function deletarLivro(id) {
        const formData = new FormData();
        formData.append('id', id); // Adiciona o ID ao corpo da requisição

        fetch('../api/deletar_livro.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                // Se a deleção foi bem sucedida, simplesmente recarregamos a lista de livros
                carregarLivros();
            } else {
                // Se falhou, mostramos um alerta com a mensagem de erro vinda do PHP
                alert(data.mensagem);
            }
        })
        .catch(error => {
            console.error('Erro ao deletar:', error);
            alert('Ocorreu um erro de comunicação ao tentar deletar o livro.');
        });
    }


    // 2. Criar uma função para buscar e exibir os livros
    function carregarLivros() {
        // Limpa a lista atual para evitar duplicatas ao recarregar
        if(listaLivrosDiv){
            listaLivrosDiv.innerHTML = '<p>Carregando livros...</p>';
        } else {
            console.error("Erro: Elemento com id 'lista-livros' não foi encontrado no HTML.");
            return; // Para a execução da função se o elemento não existir
        }

        fetch('../api/get_livros.php')
            .then(response => response.json())
            .then(data => {
                // Limpa a mensagem "Carregando..."
                listaLivrosDiv.innerHTML = ''; 

                if (data.length === 0) {
                    listaLivrosDiv.innerHTML = '<p>Nenhum livro cadastrado.</p>';
                    return;
                }

                // Itera sobre cada livro recebido e cria o card HTML
                data.forEach(livro => {
                    const card = document.createElement('div');
                    card.className = 'livro-card';
                    card.innerHTML = `
                        <h3>${livro.titulo}</h3>
                        <p><span>Autor:</span> ${livro.autor}</p>
                        <p><span>Ano:</span> ${livro.ano}</p>
                        <p><span>Gênero:</span> ${livro.genero}</p>
                        <button class="btn-deletar" data-id="${livro.id}">Deletar</button>
                    `;
                    listaLivrosDiv.appendChild(card);
                });
            })
            .catch(error => {
                // Executa se houver um erro de rede ou de comunicação com a API
                console.error('Erro ao buscar os livros:', error);
                listaLivrosDiv.innerHTML = '<p>Ocorreu um erro ao carregar a lista de livros.</p>';
            });
    }

    // 3. Chamar a função para carregar os livros assim que a página estiver pronta
    carregarLivros();

});