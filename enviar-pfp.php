<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="css/enviar-pfp.css">
    <title>Enviar Foto de Perfil</title>
</head>
<body>
    <form class="container" action="upload.php" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="username">Usuário:</label>
        <input type="text" id="username" name="username"><br>

        <label for="password">Senha:</label>
        <input type="password" id="password" name="password"><br>
        <label for="image">Escolher imagem:</label>
        <input type="file" class="form-control-file" id="image" name="image">
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
    </form>

<script>
    // Obter os valores digitados pelo usuário
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // Concatenar usuário e senha em uma string
    const credentials = `${username}:${password}`;

    // Codificar as credenciais para o formato Base64
    const encodedCredentials = btoa(credentials);


    // fetch para mandar a foto de perfil
    fetch('http://localhost/api-php/api.php/?endpoint=users/profile-image', {
        method: 'POST',
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

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>