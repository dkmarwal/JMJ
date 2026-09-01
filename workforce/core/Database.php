<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Multi-Tenant Aware Database Access Layer
 */

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

class Database {
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        $ports = [WF_DB_PORT, 3308, 3306, 3307];
        $ports = array_unique($ports);
        $connected = false;
        $lastException = null;

        foreach ($ports as $port) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                WF_DB_HOST,
                $port,
                WF_DB_DATABASE
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone = '+05:30'"
            ];

            try {
                $this->pdo = new PDO($dsn, WF_DB_USERNAME, WF_DB_PASSWORD, $options);
                $connected = true;
                break;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }

        if (!$connected) {
            throw new RuntimeException("Workforce Database Connection Failed: " . ($lastException ? $lastException->getMessage() : 'Unable to reach MySQL'));
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO {
        return $this->pdo;
    }

    public function query(string $sql, array $params = []): PDOStatement {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (WF_APP_DEBUG) {
                throw new RuntimeException("DB Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            }
            error_log("DB Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            throw new RuntimeException("A database error occurred while processing your request.");
        }
    }

    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params)->fetch();
        return $result === false ? null : $result;
    }

    public function fetchColumn(string $sql, array $params = [], int $columnIndex = 0): mixed {
        return $this->query($sql, $params)->fetchColumn($columnIndex);
    }

    public function insert(string $table, array $data): string|int {
        $columns = array_keys($data);
        $fields = implode('`, `', $columns);
        $placeholders = ':' . implode(', :', $columns);

        $sql = "INSERT INTO `{$table}` (`{$fields}`) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return $this->pdo->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int {
        $setClauses = [];
        $params = [];

        foreach ($data as $column => $value) {
            $paramName = 'set_' . $column;
            $setClauses[] = "`{$column}` = :{$paramName}";
            $params[$paramName] = $value;
        }

        $setString = implode(', ', $setClauses);
        $sql = "UPDATE `{$table}` SET {$setString} WHERE {$where}";
        $allParams = array_merge($params, $whereParams);

        $stmt = $this->query($sql, $allParams);
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $whereParams = []): int {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount();
    }

    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool {
        return $this->pdo->commit();
    }

    public function rollBack(): bool {
        return $this->pdo->rollBack();
    }

    public function transaction(callable $callback): mixed {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
