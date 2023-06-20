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
    exit;
}

// Verifique o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

// Defina a resposta padrão
$response = array(
    'status' => 'error',
    'message' => 'Invalid request'
);

// Verifique o método e o endpoint para executar a lógica da API
if ($method == 'GET') {

    // Verifique o endpoint solicitado
    $endpoint = $_GET['endpoint'];

    // Verifique os parâmetros da requisição
    $params = $_GET;

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
        // Execute a consulta para obter os dados dos usuários
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);
        
        // Verifique se a consulta retornou resultados
        if ($result->num_rows > 0) {
            $products = array();

            // Itere pelos resultados e adicione os usuários ao array
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
    // Verifique o endpoint solicitado
    $endpoint = $_POST['endpoint'];

    // Verifique os parâmetros da requisição
    $params = $_POST;

    if ($endpoint == 'users/profile-image') {
        // Verifica se o arquivo foi enviado corretamente
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
            $targetDir = './uploads';
            $targetFile = $targetDir . basename($_FILES['imageFile']['name']);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Verifica se o arquivo é uma imagem
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowedExtensions)) {
            
                // Move o arquivo para o diretório de armazenamento
                if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $targetFile)) {

                    // Obtém o nome do arquivo
                    $filename = basename($_FILES['imageFile']['name']);
                    
                    // Insere o nome do arquivo na coluna 'profile_picture' da tabela 'users'
                    $sql = "UPDATE users SET profile_picture = '$filename' WHERE id=1";

                    if ($conn->query($sql) === TRUE) {
                        echo 'Imagem enviada e inserida no banco de dados com sucesso.';
                    } else {
                        echo 'Erro ao inserir a imagem no banco de dados: ' . $conn->error;
                    }  
                } else {
                    echo 'Erro ao mover o arquivo para o diretório de armazenamento.';
                }
            } else {
                echo 'Apenas arquivos de imagem são permitidos.';
            }
        } else {
            echo 'Erro no envio do arquivo.';
        }
        $conn->close();
    }
}

// Return the response as JSON
echo json_encode($response);
?>
