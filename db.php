<?php

class myDB {
    private $servername = "localhost";
    private $username = "root";
    private $password = "root";
    private $dbname = "quizez";
    private $connection;
    private static $instance;

    private function __construct() {
        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection = $conn;
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getInstance()
    {
        if(!self::$_instance) // If no instance then make one
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function get_data($sql) {
        $conn = $this->connection();
        return $conn->query($sql);
    }

    public function exec($sql) {
        $sql = "INSERT INTO questions (type, before_text, after_text, values_text)
          VALUES ('type', 'Before', 'After', 'Values')";
        $conn = $this->connection();
        $conn->exec($sql);
    }
}
