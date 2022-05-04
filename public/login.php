<?php declare(strict_types=1);

require '../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$postParams = (array) json_decode(file_get_contents('php://input'), true);

if (! array_key_exists('username', $postParams)
    || ! array_key_exists('password', $postParams)) {

        http_response_code(400);
        echo json_encode(['message' => 'Missing login credentials']);
        exit;
}

$database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

$userGateway = new UserGateway($database);

$user = $userGateway->getByUsername($postParams['username']);

if (false === $user) {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid authentication']);
    exit;
}

if (false === password_verify($postParams['password'], $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid authentication']);
    exit;
}

$payload = ['id' => $user['id'], 'name' => $user['name']];

echo json_encode(['access_token' => base64_encode(json_encode($payload))]);
