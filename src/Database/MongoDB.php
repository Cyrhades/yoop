<?php

namespace Yoop\Database;

use Yoop\GenericSingleton;
use MongoDB\Client;

class MongoDB extends GenericSingleton implements IDatabase
{
    private $db;

    // En mettant le constructeur en visibilité protected on s'assure
    // que nous ne pourrons pas instancier cette classe depuis l'extérieur
    protected function __construct() 
    {
        $client = new Client($_ENV['MYONGODB_HOST']);
        $this->db = $client->selectDatabase($_ENV['MYONGODB_DBNAME']);
    }

    public function getDatabase()
    {
        return $this->db;
    }
}