<?php

namespace Yoop;

class Session
{
    public function __construct()
    {   
        $bStatut = false;
        if (php_sapi_name() !== 'cli' ) {
            $bStatut = (session_status() === PHP_SESSION_ACTIVE ? true : false);
        }    
        if ($bStatut === false) session_start();
    }
}
