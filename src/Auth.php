<?php declare(strict_types=1);

class Auth
{
    private int $userId;

    public function __construct(private UserGateway $gateway) {}

    public function authenticateAPIKey(): bool
    {
        if (empty($_SERVER['HTTP_X_API_KEY'])) {  
            http_response_code(400);
            echo json_encode(['message' => 'Missing API Key']);
            
            return false;
        }

        if (false === $user = $this->gateway->getByAPIKey($_SERVER['HTTP_X_API_KEY'])) {
            http_response_code(401);
            echo json_encode(['message' => 'API key is invalid']);
            
            return false;
        }

        $this->userId = $user['id'];

        return true;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
