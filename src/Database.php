<?php declare(strict_types=1);

class Database
{
    private ?PDO $connection = null;

    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $password
    ) {}

    public function getConnection(): PDO
    {
        return $this->connection ??= new PDO(
            "mysql:host={$this->host};dbname={$this->name};charset=utf8", 
            $this->user, 
            $this->password, 
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]
        );
    }
}
