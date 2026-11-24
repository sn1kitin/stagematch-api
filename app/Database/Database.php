<?php
namespace App\Database;

use PDO;
use PDOStatement;

class Database
{
    private string $host = '127.0.0.1';
    private string $dbName = 'stagematch_api_db';
    private string $user = 'root';
    private string $pass = '';

    private PDO $pdo;
    private ?PDOStatement $stmt = null;

    public function __construct()
    {
        $file = __DIR__ . '/../../config.php';
        if (file_exists($file)) {
            $config = require $file;
            $this->host = $config['host'];
            $this->dbName = $config['database'];
            $this->user = $config['user'];
            $this->pass = $config['password'];
        }

        $this->pdo = new PDO(
            "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4",
            $this->user,
            $this->pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public function query(string $sql, array $params = []): void
    {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($params);
    }

    public function fetch(): ?array
    {
        $row = $this->stmt->fetch();

        return $row === false ? null : $row;
    }

    public function lastId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    public function fetchAll(): array
    {
        return $this->stmt->fetchAll();
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }
}
