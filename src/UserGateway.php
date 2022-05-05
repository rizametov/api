<?php declare(strict_types=1);

class UserGateway
{
    private PDO $connection;

    private const USERS_TABLE = 'users';

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

    public function getByUsername(string $username): array|false
    {
        $sql = sprintf('SELECT * FROM %s WHERE username = :username', self::USERS_TABLE);

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':username', $username, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|false
    {
        $sql = sprintf('SELECT * FROM %s WHERE id = :id', self::USERS_TABLE);

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
