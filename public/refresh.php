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

$userId = $payload['sub'];

$database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

$refreshTokenGateway = new RefreshTokenGateway($database, $_ENV['SECRET_KEY']);

if (false === $refreshTokenGateway->getByToken($postParams['token'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid token (not on whitelist)']);
    exit;
}

$userGateway = new UserGateway($database);

if (false === $user = $userGateway->getById($userId)) {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid authentication']);
    exit;
}

// Generate JWT
$codec = new JWTCodec($_ENV['SECRET_KEY']);

$refreshTokenExpiry = time() + 432000;
$refreshToken = $codec->encode(['sub' => $user['id'], 'exp' => $refreshTokenExpiry]);

echo json_encode([
    'jwt_token' => $codec->encode(['sub' => $user['id'], 'name' => $user['name'], 'exp' => time() + 30]),
    'refresh_token' => $refreshToken
]);

$refreshTokenGateway->delete($postParams['token']);

$refreshTokenGateway->create($refreshToken, $refreshTokenExpiry);
