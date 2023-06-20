<?php
// Defina o cabeçalho para permitir o acesso de outros domínios (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

// Defina o nome de usuário e senha permitidos
$usuarioPermitido = 'admin';
$senhaPermitida = 'admin';

// Verifica as credenciais fornecidas pelo cliente
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $usuarioPermitido || $_SERVER['PHP_AUTH_PW'] !== $senhaPermitida) {
    
    // Credenciais inválidas, retorna erro de autenticação
    header('HTTP/1.0 401 Unauthorized');
    
    $response = array(
        'status' => 'error',
        'message' => 'Credenciais inválidas'
    );

    echo json_encode($response);
    exit;
}

// Credenciais válidas, continuar com o processamento da API

// Conecte-se ao banco de dados MySQL
$servername = "localhost";
$username = "teste";
$password = "teste";
$dbname = "teste-api";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique se a conexão foi estabelecida com sucesso
if ($conn->connect_error) {
    $response = array(
        'status' => 'error',
        'message' => 'Failed to connect to MySQL: ' . $conn->connect_error
    );
    
    echo json_encode($response);

    // Feche a conexão com o banco de dados
    $conn->close();
    exit;
}

// Verifique o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

// Verifique o endpoint solicitado
$endpoint = $_GET['endpoint'];

// Defina a resposta padrão
$response = array(
    'status' => 'error',
    'message' => 'Invalid request'
);

// Verifique o método e o endpoint para executar a lógica da API
if ($method == 'GET') {

    if ($endpoint == 'users') {
        // Execute a consulta para obter os dados dos usuários
        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);

        // Verifique se a consulta retornou resultados
        if ($result->num_rows > 0) {
            $users = array();

            // Itere pelos resultados e adicione os usuários ao array
            while ($row = $result->fetch_assoc()) {
                $user = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email']
                );

                $users[] = $user;
            }

            $response = array(
                'status' => 'success',
                'users' => $users
            );
        } else {
            $response = array(
                'status' => 'success',
                'users' => []
            );
        }

        // Feche a conexão com o banco de dados
        $conn->close();
    }

    if ($endpoint == 'products/images') {
        // Execute a consulta para obter os dados dos produtos
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);
        
        // Verifique se a consulta retornou resultados
        if ($result->num_rows > 0) {
            $products = array();

            // Itere pelos resultados e adicione os produtos ao array
            while ($row = $result->fetch_assoc()) {

                $product = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'image' => $row['image'],
                    'imageUrl' => $row['image_url'],
                );

                $products[] = $product;
            }

            $response = array(
                'status' => 'success',
                'products' => $products
            );
        } else {
            $response = array(
                'status' => 'success',
                'products' => []
            );
        }

        // Feche a conexão com o banco de dados
        $conn->close();
    }
}

if ($method == 'POST') {
    if ($endpoint == 'users/profile-image') {
        // Verifica se o arquivo foi enviado corretamente
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
            $tempPath = $_FILES['imageFile']['tmp_name'];
            $originalFilename = $_FILES['imageFile']['name'];

            // // Salve o arquivo em algum diretório
            // $targetPath = './img/uploads/' . $originalFilename;
            // move_uploaded_file($tempPath, $targetPath);

            // Obtenha os valores dos outros dados do formulário
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $response = array(
                'status' => 'success',
                'message' => 'Image uploaded successfully',
            );

            // Obter o conteúdo binário do arquivo
            $imageData = file_get_contents($tempPath);

            // Preparar a consulta SQL para inserir os dados na tabela
            $sql = "UPDATE users SET profile_picture = (?) WHERE email = (?) AND password = (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $imageData, $email, $senha);
            $stmt->execute();

            // Verificar se a inserção foi bem-sucedida
            if ($stmt->affected_rows > 0) {
                $response = array(
                    'status' => 'success',
                    'message' => 'Image uploaded and saved to the database',
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'Failed to save image to the database',
                );
            }

            $stmt->close();
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Failed to upload image',
            );
        }

        $conn->close();
    }
}

// Return the response as JSON
echo json_encode($response);
?>
