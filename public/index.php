<?php declare(strict_types=1);

require '../src/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ('/api/tasks' !== substr($path, 0, 10)) {
    http_response_code(404);
    echo json_encode(['message' => 'Endpoint not found']);
    exit;
}

preg_match('/^\/(?P<id>[\d]+)$/', substr($path, 10), $match);

$database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

$userGateway = new UserGateway($database);

$auth = new Auth($userGateway);

if (false === $auth->authenticateAccessToken()) exit;

$taskGateway = new TaskGateway($database);
$taskController = new TaskController($taskGateway, $auth->getUserId());

$taskController->processRequest($_SERVER['REQUEST_METHOD'], $match['id'] ?? null);
