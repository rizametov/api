<?php declare(strict_types=1);

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    
    $connection = (new Database(
            $_ENV['DB_HOST'],
            $_ENV['DB_NAME'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        ))->getConnection();

    $sql = 'INSERT INTO user (name, username, password_hash, api_key)'
        . ' VALUES (:name, :username, :password_hash, :api_key)';

    $stmt = $connection->prepare($sql);

    $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $apiKey = bin2hex(random_bytes(16));

    $stmt->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
    $stmt->bindValue(':username', $_POST['username'], PDO::PARAM_STR);
    $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
    $stmt->bindValue(':api_key', $apiKey, PDO::PARAM_STR);

    $stmt->execute();

    echo 'Registration completed successfully';
    echo '<br>';
    echo 'Your Api Key: ', $apiKey;

    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Register</title>
        <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    </head>
<body>
    <main class="container">
        <h1>Register</h1>
        <form method="post">
            <label for="name">
                Name
                <input type="text" name="name" id="name">
            </label>
            <label for="username">
                Username
                <input type="text" name="username" id="username">
            </label>
            <label for="password">
                Password
                <input type="password" name="password" id="password">
            </label>
            <button>Register</button>
        </form>
    </main>
</body>
</html>

