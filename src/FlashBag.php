<?php

namespace Yoop;

class FlashBag extends Session 
{

    public function get(?string $name = null)
    {
        $flashbag = [];
        if(isset($_SESSION['_flashbag'])) {
            if($name === null) {
                    $flashbag = $_SESSION['_flashbag'];
                    unset($_SESSION['_flashbag']);
            }
            elseif(isset($_SESSION['_flashbag'][$name])) {
                $flashbag = $_SESSION['_flashbag'][$name] ?? null;
                unset($_SESSION['_flashbag'][$name]);
            }
        }
        return $flashbag;
    }

    public function set(string $name, $value = null)
    {            
        $_SESSION['_flashbag'][$name] = $value;
    }
}