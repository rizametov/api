<?php declare(strict_types=1);

class UserGateway
{
    private PDO $connection;

    private const USERS_TABLE = 'user';

    public function __construct(Database $database)
    {
        $this->connection = $database->getConnection();
    }

    public function getByAPIKey(string $key): array|false
    {
        $sql = sprintf('SELECT * FROM %s WHERE api_key = :api_key', self::USERS_TABLE);

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':api_key', $key, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    }
}
