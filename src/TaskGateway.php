<?php declare(strict_types=1);

class TaskGateway
{
    private PDO $connection;

    private const TASKS_TABLE = 'tasks';

    public function __construct(Database $database)
    {
        $this->connection = $database->getConnection();
    }

    public function all(int $userId): array
    {
        $sql = sprintf('SELECT * FROM %s WHERE user_id = :user_id ORDER BY name', self::TASKS_TABLE);

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get(int $userId, string $id): array|false
    {
        $sql = sprintf('SELECT * FROM %s WHERE id = :id AND user_id = :user_id', self::TASKS_TABLE);

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(int $userId, array $data): string
    {
        $sql = sprintf(
            'INSERT INTO %s (name, priority, is_completed, user_id) 
            VaLUES (:name, :priority, :is_completed, :user_id)', 
            self::TASKS_TABLE
        );

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(
            ':priority', 
            $data['priority'] ?? null, 
            empty($data['priority']) ? PDO::PARAM_NULL : PDO::PARAM_INT
        );
        $stmt->bindValue(':is_completed', $data['is_completed'] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        $stmt->execute();

        return $this->connection->lastInsertId();
    }

    public function update(int $userId, string $id, array $data): int
    {
        $fields = [];

        if (! empty($data['name'])) {
            $fields['name'] = [$data['name'], PDO::PARAM_STR];
        }

        if (array_key_exists('priority', $data)) {
            $fields['priority'] = [
                $data['priority'], 
                $data['priority'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            ];
        }

        if (array_key_exists('is_completed', $data)) {
            $fields['is_completed'] = [$data['is_completed'], PDO::PARAM_BOOL];
        }

        if (empty($fields)) {
            return 0;
        } else {
            $sets = array_map(
                fn ($value) => $value . ' = :' .$value, 
                array_keys($fields)
            );

            $sql = sprintf('UPDATE %s SET ' . implode (', ', $sets) 
                        . ' WHERE id = :id AND user_id = :user_id', self::TASKS_TABLE);

            $stmt = $this->connection->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            foreach ($fields as $name => $values) {
                $stmt->bindValue($name, $values[0], $values[1]);
            }

            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->rowCount();
        }
    }

    public function delete(int $userId, string $id): int
    {
        $sql = sprintf('DELETE FROM %s WHERE id = :id AND user_id = :user_id', self::TASKS_TABLE);

        $stmt = $this->connection->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}
