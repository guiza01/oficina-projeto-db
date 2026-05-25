<?php


class Database
{
    private $mysqli;
    private static $instance = null;

    private function __construct()
    {
        $this->connect();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect()
    {
        $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

        if ($this->mysqli->connect_error) {
            die("Erro de conexão: " . $this->mysqli->connect_error);
        }

        $this->mysqli->set_charset("utf8mb4");
    }

    public function query($sql)
    {
        return $this->mysqli->query($sql);
    }

    public function prepare($sql)
    {
        return $this->mysqli->prepare($sql);
    }

    public function getConnection()
    {
        return $this->mysqli;
    }

    public function escape($string)
    {
        return $this->mysqli->real_escape_string($string);
    }

    public function lastInsertId()
    {
        return $this->mysqli->insert_id;
    }

    public function affectedRows()
    {
        return $this->mysqli->affected_rows;
    }

    public function error()
    {
        return $this->mysqli->error;
    }

    public function beginTransaction()
    {
        $this->mysqli->begin_transaction();
    }

    public function commit()
    {
        $this->mysqli->commit();
    }

    public function rollback()
    {
        $this->mysqli->rollback();
    }

    public function close()
    {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
