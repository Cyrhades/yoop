<?php

namespace Yoop\Database;

use Yoop\GenericSingleton;
use Predis\Client;

class Redis extends GenericSingleton implements IDatabase
{
    private $db;

    // En mettant le constructeur en visibilité protected on s'assure
    // que nous ne pourrons pas instancier cette classe depuis l'extérieur
    protected function __construct() 
    {
        $this->db = new Client($_ENV['REDIS_HOST'], [
            'parameters' => [
                'password' => $_ENV['REDIS_PASSWORD']
            ]
        ]);
    }

    public function getDatabase()
    {
        return $this->db;
    }
    
}