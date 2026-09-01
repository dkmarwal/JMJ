<?php
/**
 * JMJ Enterprises Solutions - PDO Database Wrapper
 * Singleton Connection Pattern with Prepared Statement Helpers
 */

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use PDOStatement;
use Exception;

class Database {
    private static ?Database $instance = null;
    private ?PDO $pdo = null;

    private function __construct() {
        $host = (string)env('DB_HOST', '127.0.0.1');
        $port = (int)env('DB_PORT', 3308);
        $dbname = (string)env('DB_DATABASE', 'jmj_enterprise_db');
        $user = (string)env('DB_USERNAME', 'root');
        $pass = (string)env('DB_PASSWORD', '');

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];

        try {
            // First attempt with specified port
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Fallback to standard 3306 or 3307 if 3308 failed
            $altPorts = [3306, 3307, 3308];
            $connected = false;
            foreach ($altPorts as $altPort) {
                if ($altPort === $port) continue;
                try {
                    $dsn = "mysql:host={$host};port={$altPort};dbname={$dbname};charset=utf8mb4";
                    $this->pdo = new PDO($dsn, $user, $pass, $options);
                    $connected = true;
                    break;
                } catch (PDOException) {
                    continue;
                }
            }

            if (!$connected && $this->pdo === null) {
                // If database doesn't exist yet, connect to server without dbname to allow creation
                try {
                    $serverDsn = "mysql:host={$host};port={$port};charset=utf8mb4";
                    $serverPdo = new PDO($serverDsn, $user, $pass, $options);
                    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $this->pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $user, $pass, $options);
                } catch (PDOException $ex) {
                    if (APP_DEBUG) {
                        die("<div style='font-family:sans-serif;padding:20px;background:#fee2e2;color:#991b1b;border-radius:8px;'><strong>Database Connection Error:</strong> " . e($ex->getMessage()) . "</div>");
                    } else {
                        die("<div style='font-family:sans-serif;padding:20px;text-align:center;'>Service temporarily unavailable. Please try again later.</div>");
                    }
                }
            }
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO {
        return $this->pdo;
    }

    /**
     * Execute a prepared query with parameters
     */
    public function query(string $sql, array $params = []): PDOStatement {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                throw new Exception("Database Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            }
            throw new Exception("Database error occurred.");
        }
    }

    /**
     * Fetch a single row as associative array
     */
    public function fetchOne(string $sql, array $params = []): ?array {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    /**
     * Fetch all matching rows
     */
    public function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch a single column value (e.g. COUNT(*))
     */
    public function fetchColumn(string $sql, array $params = [], int $columnIndex = 0): mixed {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($columnIndex);
    }

    /**
     * Insert a record into a table
     */
    public function insert(string $table, array $data): int {
        $columns = array_keys($data);
        $escapedCols = array_map(fn($c) => "`" . str_replace("`", "", $c) . "`", $columns);
        $placeholders = array_map(fn($c) => ":" . $c, $columns);

        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            str_replace("`", "", $table),
            implode(", ", $escapedCols),
            implode(", ", $placeholders)
        );

        $this->query($sql, $data);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update table rows matching a WHERE condition
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int {
        $sets = [];
        $params = [];

        foreach ($data as $col => $val) {
            $paramKey = "set_" . $col;
            $sets[] = "`" . str_replace("`", "", $col) . "` = :" . $paramKey;
            $params[$paramKey] = $val;
        }

        $sql = sprintf(
            "UPDATE `%s` SET %s WHERE %s",
            str_replace("`", "", $table),
            implode(", ", $sets),
            $where
        );

        $mergedParams = array_merge($params, $whereParams);
        $stmt = $this->query($sql, $mergedParams);
        return $stmt->rowCount();
    }

    /**
     * Delete table rows
     */
    public function delete(string $table, string $where, array $whereParams = []): int {
        $sql = sprintf("DELETE FROM `%s` WHERE %s", str_replace("`", "", $table), $where);
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount();
    }

    public function lastInsertId(): int {
        return (int)$this->pdo->lastInsertId();
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
}
