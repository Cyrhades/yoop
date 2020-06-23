<?php

namespace Yoop;

class FlashBag extends Session 
{

    public function get(string $name)
    {
        return $_SESSION['_flashbag'][$name] ?? null;
    }

    public function set(string $name, $value = null)
    {            
        $_SESSION['_flashbag'][$name] = $value;
    }
}