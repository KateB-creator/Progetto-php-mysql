<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'sunnee_db';
    private $username = 'root'; // Cambia se necessario
    private $password = '';     // Cambia se necessario
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Errore di connessione: ' . $e->getMessage();
        }
        return $this->conn;
    }
}
