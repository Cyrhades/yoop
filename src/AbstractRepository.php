<?php

namespace Yoop;

use Yoop\Database\MongoDB;
use Yoop\Database\MySql;
use Yoop\Database\Redis;

abstract class AbstractRepository
{
    protected $db;

    public function __construct(string $typeDB)
    {
        switch($typeDB) {
            case 'sql' :
            case 'mysql' :
                $connect = MySql::getInstance();
            break;
            case 'mongo' :
            case 'mongodb' :
                $connect = MongoDB::getInstance();
                break;
            case 'redis' :
            case 'predis' :
                $connect = Redis::getInstance();
                break;
        }
        if($connect)
            $this->db = $connect->getDatabase();
        else
            throw new Exception('No database Connect');
    }
}
