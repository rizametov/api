<?php declare(strict_types=1);

require '../vendor/autoload.php';

header('Content-type: application/json; charset=UTF-8');

set_error_handler('ErrorHandler::handleError');
set_exception_handler('ErrorHandler::handleException');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ('/api/tasks' !== substr($path, 0, 10)) {
    http_response_code(404);
    exit;
}

preg_match('/^\/(?P<id>[\d]+)$/', substr($path, 10), $match);

(new TaskController())->processRequest($_SERVER['REQUEST_METHOD'], $match['id'] ?? null);
