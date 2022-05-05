<?php declare(strict_types=1);

class RefreshTokenGateway
{
    private PDO $connection;

    private const REFRESH_TOKENS_TABLE = 'refresh_tokens';

    public function __construct(Database $database, private string $key)
    {
        $this->connection = $database->getConnection();
    }

    public function create(string $token, int $expiry): bool
    {
        $hash = hash_hmac('sha256', $token, $this->key);

        $sql = sprintf('INSERT INTO %s (token_hash, expires_at) 
                        VALUES (:token_hash, :expires_at)', self::REFRESH_TOKENS_TABLE);

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':token_hash', $hash, PDO::PARAM_STR);

        $stmt->bindValue(':expires_at', $expiry, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete(string $token): int
    {
        $hash = hash_hmac('sha256', $token, $this->key);

        $sql = sprintf('DELETE FROM %s WHERE token_hash = :token_hash', self::REFRESH_TOKENS_TABLE);

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':token_hash', $hash, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getByToken(string $token): array|false
    {
        $hash = hash_hmac('sha256', $token, $this->key);

        $sql = sprintf('SELECT * FROM %s WHERE token_hash = :token_hash', self::REFRESH_TOKENS_TABLE);

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':token_hash', $hash, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteExpired(): int
    {
        $sql = sprintf('DELETE FROM %s WHERE expires_at < UNIX_TIMESTAMP()', self::REFRESH_TOKENS_TABLE);

        $stmt = $this->connection->query($sql);

        return $stmt->rowCount();
    }
}
