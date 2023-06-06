<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Autenticação de API</title>
</head>
<body>
    <header>
        <img src="img/logo.png" alt="logomarca">
        <h1>Autenticação de API</h1>
    </header>

    <!-- Formulário de login -->
    <form id="loginForm" class="cont-form">
        <label for="username">Usuário:</label>
        <input type="text" id="username" name="username"><br>

        <label for="password">Senha:</label>
        <input type="password" id="password" name="password"><br>

        <button type="submit">Consultar</button>
    </form>

    <!-- Div para exibir a resposta da API -->
    <div id="response" class="container">
        <h1>Resposta da consulta</h1>
        <h2>Usuários</h2>
        <pre class="response" id="response-users"></pre>
        <h2>Produtos</h2>
        <pre class="response" id="response-products"></pre>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const responseUsers = document.querySelector('#response-users');
        const responseProducts = document.querySelector('#response-products');

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            // Obter os valores digitados pelo usuário
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Concatenar usuário e senha em uma string
            const credentials = `${username}:${password}`;

            // Codificar as credenciais para o formato Base64
            const encodedCredentials = btoa(credentials);
            
            // Fazer uma solicitação HTTP para a API
            fetch('http://localhost/api-php/api.php/?endpoint=users', {
                headers: {
                    'Authorization': `Basic ${encodedCredentials}` // Incluir as credenciais no cabeçalho da solicitação
                }
            })
            .then(response => response.json()) // Converter a resposta em JSON
            .then(data => {
                responseUsers.textContent = JSON.stringify(data, null, 2); // Exibir a resposta da API na div
            })
            .catch(error => {
                responseUsers.textContent = 'Erro ao fazer a solicitação: ' + error.message; // Exibir mensagem de erro
            });
            

            // fetch para dados da tabela de produtos
            fetch('http://localhost/api-php/api.php/?endpoint=products/images', {
                headers: {
                    'Authorization': `Basic ${encodedCredentials}` // Incluir as credenciais no cabeçalho da solicitação
                }
            })
            .then(response => response.json()) // Converter a resposta em JSON
            .then(data => {
                responseProducts.textContent = JSON.stringify(data, null, 2); // Exibir a resposta da API na div
            })
            .catch(error => {
                responseProducts.textContent = 'Erro ao fazer a solicitação: ' + error.message; // Exibir mensagem de erro
            });

        });
    </script>
</body>
</html>
