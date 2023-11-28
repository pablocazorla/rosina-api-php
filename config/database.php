<?php
// used to get mysql database connection
class Database
{

    // specify your own database credentials
    private $host = "localhost";
    public $db_name = "rosina_api";
    private $username = "rosina_api";
    private $password = "Abril0204$$";

    // private $host = "db5013159030.hosting-data.io";
    // public $db_name = "dbs11043805";
    // private $username = "dbu421658";
    // private $password = "kA4LCfgGt9ywkB8XU5aY";

    public $conn;

    // get the database connection
    public function getConnection()
    {

        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}