<?php declare(strict_types=1);

require '../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$postParams = (array) json_decode(file_get_contents('php://input'), true);

if (! array_key_exists('token', $postParams)) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing token']);
        exit;
}

$codec = new JWTCodec($_ENV['SECRET_KEY']);

try {
    $payload = $codec->decode($postParams['token']);
} catch (Exception) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid token']);
    exit;
}

$database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

$refreshTokenGateway = new RefreshTokenGateway($database, $_ENV['SECRET_KEY']);

$refreshTokenGateway->delete($postParams['token']);
