<?php

namespace Yoop;

use PDO;

class DatabaseMySql {

    private static $db;

    private static $_instance = [];

    /** On interdit les instances directes ! */
    private function __construct($dbName = 0, array $connect = []) 
    {
        self::$db[$dbName] = new PDO(
            $connect['dsn']??'',
            $connect['login']??'',
            $connect['password']??''
        );
    }
    
    /** On interdit le clonage ! */
    private function __clone() { throw new Exception('Pas de clonage possible !'); }


    public static function getDb($dbName = 0) 
    {
        if (!array_key_exists($dbName, self::$_instance)) {
            self::getInstance($dbName);
        }
        return self::$db[$dbName] ?? null;
    }

    public static function getInstance($dbName = 0, array $connect = []) 
    {
        if(sizeof($connect) == 0) {
            $config = include dirname(__DIR__, 4) . '/app/config.php';
            $connect = $config['mysql'] ?? [];
        }
        if (! array_key_exists($dbName, self::$_instance)) {
            self::$_instance[$dbName] = new DatabaseMySql($dbName, $connect);
        }
        return self::$_instance[$dbName];
    }
}