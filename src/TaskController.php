<?php declare(strict_types=1);

class TaskController
{
    public function __construct(private TaskGateway $gateway) {}

    public function processRequest(string $method, ?string $id): void
    {
        if (null === $id) {
            switch ($method) {
                case 'GET':
                    echo json_encode($this->gateway->all());
                    
                    break;
                case 'POST':
                    $data = (array) json_decode(file_get_contents('php://input'), true);
                    
                    if (! empty($errors = $this->getValidationErrors($data))) {
                        $this->respondUnprocessableEntity($errors);
                        return;   
                    }

                    $this->respondCreated($this->gateway->create($data));
                    
                    break;
                default:
                    $this->respondMethodNotAllowed('GET, POST');
            }
        } else {
            if (false === $task = $this->gateway->get($id)) {
                $this->respondNotFound($id);
                return;
            }

            switch ($method) {
                case 'GET':
                    echo json_encode($task);

                    break;
                case 'PATCH':
                    $data = (array) json_decode(file_get_contents('php://input'), true);
                    
                    if (! empty($errors = $this->getValidationErrors($data, false))) {
                        $this->respondUnprocessableEntity($errors);
                        return;   
                    }

                    echo json_encode([
                        'massage' => 'Task updated', 
                        'rows' => $this->gateway->update($id, $data)
                    ]);

                    break;
                case 'DELETE':
                    echo json_encode([
                        'message' => 'Taks deleted',
                        'rows' => $this->gateway->delete($id)
                    ]);

                    break;
                default:
                    $this->respondMethodNotAllowed('GET, PATCH, DELETE');
            }
        }
    }

    private function getValidationErrors(array $data, bool $isNewRecord = true): array
    {
        $errors = [];

        if (true === $isNewRecord && empty($data['name'])) {
            $errors[] = 'Name is required';
        }

        if (! empty($data['priority'])) {
            if (false === filter_var($data['priority'], FILTER_VALIDATE_INT)) {
                $errors[] = 'Priority must be a number';
            }
        }

        return $errors;
    }

    private function respondMethodNotAllowed(string $allowedMethods): void
    {
        http_response_code(405);

        header('Allow: ' . $allowedMethods);
    } 

    private function respondNotFound(string $id): void
    {
        http_response_code(404);

        echo json_encode(['messahe' => sprintf('Task %s not found', $id)]);
    }

    private function respondCreated(string $id): void
    {
        http_response_code(201);

        echo json_encode(['message' => sprintf('Task %s created', $id)]);
    }

    private function respondUnprocessableEntity(array $errors): void
    {
        http_response_code(422);

        echo json_encode(['errors' => $errors]);
    }
}
