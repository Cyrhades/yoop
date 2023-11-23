<?php

namespace Yoop\Database;

use Yoop\GenericSingleton;
use PDO;

class MySql extends GenericSingleton implements IDatabase
{
    private $db;
    // En mettant le constructeur en visibilité protected on s'assure
    // que nous ne pourrons pas instancier cette classe depuis l'extérieur
    protected function __construct() 
    {
        if(!empty($_ENV['MYSQL_HOST']) && !empty($_ENV['MYSQL_DBNAME'])) {
            $dsn = "mysql:host=".$_ENV['MYSQL_HOST'].";dbname=".$_ENV['MYSQL_DBNAME'].";port=".($_ENV['MYSQL_PORT']??"3306");
            $username = $_ENV['MYSQL_USER'] ?? "root";
            $password = $_ENV['MYSQL_PASS'] ?? "";
        } 
        else if(!empty($_ENV['MYSQL_DSN'])) {
            $dsn = $_ENV['MYSQL_DSN'];
            $username = $_ENV['MYSQL_USER'] ?? "root";
            $password = $_ENV['MYSQL_PASS'] ?? "";
        } else {
            // todo : gérer via uri type mysql://root:pass@localhost:3306/dbname
        }

        $this->db = new PDO($dsn, $username, $password);
    }

    public function getDatabase() 
    {
        return $this->db;
    }
}