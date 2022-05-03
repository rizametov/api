<?php declare(strict_types=1);

class TaskController
{
    public function processRequest(string $method, ?string $id): void
    {
        if (null === $id) {
            switch ($method) {
                case 'GET':
                    echo 'index' . PHP_EOL;
                    break;
                case 'POST':
                    echo 'create' . PHP_EOL;
                    break;
                default:
                    $this->respondMethodNotAllowed('GET, POST');
            }
        } else {
            switch ($method) {
                case 'GET':
                    echo 'show ' . $id;
                    break;
                case 'PATCH':
                    echo 'update ' . $id;
                    break;
                case 'DELETE':
                    echo 'delete ' . $id;
                    break; 
                default:
                    $this->respondMethodNotAllowed('GET, PATCH, DELETE');
            }
        }
    }

    private function respondMethodNotAllowed(string $allowedMethods): void
    {
        http_response_code(405);

        header('Allow: ' . $allowedMethods);
    } 
}
