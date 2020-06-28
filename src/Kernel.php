<?php

namespace Yoop;

class Kernel 
{
    private $router;

    public function __construct()
    {
        // @todo : rendre paramétrable
        date_default_timezone_set('Europe/Paris');
        $this->router = new Router;
    }

    public function getRouter() 
    {
        return $this->router;
    }

}