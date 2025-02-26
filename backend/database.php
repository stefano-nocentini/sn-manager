<?php

class Database
{
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbname = 'sn_manager';
    private $charset = 'utf8mb4';
    private $conn;

    public function connect()
    {
        if ($this->conn === null) {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

            if ($this->conn->connect_error) {
                die("Errore di connessione al database: " . $this->conn->connect_error);
            }

            $this->conn->set_charset($this->charset);
        }

        return $this->conn;
    }
}

?>
