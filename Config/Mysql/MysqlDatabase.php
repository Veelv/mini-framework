<?php

namespace Config\Mysql;

use Config\EnvLoader;
use Exception;
use mysqli;

define('TOKENS', __DIR__ . '/../../tokens/');

class MysqlDatabase
{
    private string $hostname;
    private string $database;
    private string $username;
    private string $password;
    private string $port;
    private string $charset;
    private $conn;
    private $env;
    private $tables;

    public function __construct()
    {
        $this->env = new EnvLoader(__DIR__ . '/../../.env');

        $this->hostname = $this->env->get('MYSQLHOSTNAME');
        $this->database = $this->env->get('MYSQLDATABASE');
        $this->username = $this->env->get('MYSQLDATAUSER');
        $this->password = $this->env->get('MYSQLDATAPASS');
        $this->port = $this->env->get('MYSQLDATAPORT', '3306');
        $this->charset = $this->env->get('MYSQLDATACHARSET', 'utf8mb4');

        // $mysqli = mysqli_init();

        // echo $mysqli->ssl_set(NULL, NULL, TOKENS."ca-certificates.crt", NULL, NULL);
    }

    public function connect()
    {        
        $this->conn = new mysqli($this->hostname, $this->username, $this->password, $this->database, $this->port);

        if ($this->conn->connect_errno) {
            throw new Exception("Falha na conexão com o banco de dados: " . $this->conn->connect_error);
        }

        mysqli_ssl_set($this->conn, NULL, NULL, TOKENS."cacert.pem", NULL, NULL);
    }

    public function disconnect()
    {
        if ($this->conn instanceof mysqli) {
            $this->conn->close();
            $this->conn = null;
        }
    }

    public function prepare($sql)
    {
        if (!$this->conn) {
            $this->connect();
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a consulta: " . $this->conn->error);
        }
        return $stmt;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->prepare($sql);

        if (count($params) > 0) {
            $this->bindParams($stmt, $params);
        }

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Erro na consulta ao banco de dados: " . mysqli_stmt_error($stmt));
        }

        return $stmt;
    }

    public function select($table, $columns = "*", $where = "", $params = [])
    {
        $sql = "SELECT $columns FROM $table";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        $stmt = $this->query($sql, $params);
        $result = mysqli_stmt_get_result($stmt);
        if (!$result) {
            throw new Exception("Erro na consulta ao banco de dados: " . mysqli_error($this->conn));
        }
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $data;
    }

    public function selectOne($table, $columns = "*", $where = "", $params = [])
    {
        $sql = "SELECT $columns FROM $table";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        $stmt = $this->query($sql, $params);
        $result = mysqli_stmt_get_result($stmt);
        if (!$result) {
            throw new Exception("Erro na consulta ao banco de dados: " . mysqli_error($this->conn));
        }
        $data = mysqli_fetch_assoc($result);
        return $data;
    }

    public function insert($table, $data)
    {
        try {
            $columns = implode(", ", array_keys($data));
            $values = implode(", ", array_fill(0, count($data), "?"));
            $sql = "INSERT INTO $table ($columns) VALUES ($values)";
            return $this->query($sql, array_values($data));
        } catch (Exception $e) {
            throw new Exception("Erro ao inserir: " . $e->getMessage());
        }
    }

    public function update($table, $data, $where = "", $params = [])
    {
        try {
            $setValues = [];
            foreach ($data as $column => $value) {
                $setValues[] = "$column = ?";
            }
            $setClause = implode(", ", $setValues);
            $sql = "UPDATE $table SET $setClause";
            if (!empty($where)) {
                $sql .= " WHERE $where";
            }
            $params = array_merge(array_values($data), $params);
            return $this->query($sql, $params);
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar: " . $e->getMessage());
        }
    }

    public function count($table, $where = '', $params = [])
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM $table";
            if (!empty($where)) {
                $sql .= " WHERE $where";
            }
            $stmt = $this->query($sql, $params);
            $result = mysqli_stmt_get_result($stmt);
            if (!$result) {
                throw new Exception("Erro na consulta ao banco de dados: " . mysqli_error($this->conn));
            }
            $data = mysqli_fetch_assoc($result);
            if (!$data) {
                throw new Exception("Nenhum resultado encontrado.");
            }
            return (int) $data['total'];
        } catch (Exception $e) {
            throw new Exception("Erro ao consultar: " . $e->getMessage());
        }
    }

    public function delete($table, $where = "", $params = [])
    {
        try {
            $sql = "DELETE FROM $table";
            if (!empty($where)) {
                $sql .= " WHERE $where";
            }
            return $this->query($sql, $params);
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar: " . $e->getMessage());
        }
    }

    public function fetchArray($stmt)
    {
        return mysqli_stmt_fetch($stmt);
    }

    public function numRows($stmt)
    {
        return mysqli_stmt_num_rows($stmt);
    }

    private function bindParams($stmt, $params)
    {
        $types = "";
        $bindParams = [$stmt, &$types];

        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= "i";
            } elseif (is_float($param)) {
                $types .= "d";
            } elseif (is_string($param)) {
                $types .= "s";
            } else {
                $types .= "b";
            }
            $bindParams[] = $param;
        }

        if (!mysqli_stmt_bind_param(...$bindParams)) {
            throw new Exception("Erro ao vincular os parâmetros da consulta: " . mysqli_stmt_error($stmt));
        }
    }

    public function beginTransaction()
    {
        $this->conn->begin_transaction();
    }

    public function commit()
    {
        $this->conn->commit();
    }

    public function rollback()
    {
        $this->conn->rollback();
    }

    public function getLastInsertId()
    {
        return $this->conn->insert_id;
    }
}