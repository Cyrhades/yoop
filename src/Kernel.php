<?php

namespace Yoop;

class Kernel 
{
    private $router;

    public function __construct()
    {
        $this->router = new Router;
    }

    public function getRouter() 
    {
        return $this->router;
    }

}