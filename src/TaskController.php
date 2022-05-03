<?php declare(strict_types=1);

class TaskController
{
    public function processRequest(string $method, ?string $id): void
    {
        if (null === $id) {
            if ($method === 'GET') {
                echo 'index' . PHP_EOL;
            } elseif ($method === 'POST') {
                echo 'create' . PHP_EOL;
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
            }
        }
    }
}
