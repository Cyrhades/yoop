<?php

namespace Yoop;

class Kernel 
{
    private $router;

    public function __construct()
    {
        if(!defined("ROOT_DIR")) {
            define("ROOT_DIR", dirname(__DIR__,4));
        }

        StaticDotEnv::load(ROOT_DIR.'/.env');

        $this->router = new Router;
    }

    public function getRouter() 
    {
        return $this->router;
    }
}