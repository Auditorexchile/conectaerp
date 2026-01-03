<?php
/**
 * CONECTA ERP - GESTOR DE BASE DE DATOS
 *
 * Clase para manejar conexiones y consultas a la base de datos
 */

class Database {
    private static $instance = null;
    private $pdo = null;
    private $config = null;

    private function __construct() {
        $this->config = require CONFIG_PATH . '/database.php';
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        $conn = $this->config['connections'][$this->config['default']];

        try {
            $dsn = "{$conn['driver']}:host={$conn['host']};port={$conn['port']};dbname={$conn['database']}";

            if ($conn['driver'] === 'pgsql') {
                $dsn .= ";options='--client_encoding=UTF8'";
            }

            $this->pdo = new PDO(
                $dsn,
                $conn['username'],
                $conn['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor contacte al administrador.");
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . json_encode($params));
            throw $e;
        }
    }

    public function select($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function selectOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $this->query($sql, $data);
        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = :{$column}";
        }
        $set = implode(', ', $set);

        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";

        return $this->query($sql, array_merge($data, $whereParams))->rowCount();
    }

    public function delete($table, $where, $whereParams = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $whereParams)->rowCount();
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollback();
    }

    public function getPDO() {
        return $this->pdo;
    }
}

// Alias global
function db() {
    return Database::getInstance();
}
