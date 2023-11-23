<?php

namespace Yoop;

abstract class GenericSingleton 
{
    protected static $oInstance = array();
    // on empeche aussi le clonage.
    private function __clone() {}
   
    public static function getInstance() 
    {
        $class = get_called_class();
        if (! array_key_exists($class, self::$oInstance)) {
            self::$oInstance[$class] = new $class();
        }
        return self::$oInstance[$class];
    }
}