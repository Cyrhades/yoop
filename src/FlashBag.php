<?php

namespace Yoop;

class FlashBag extends Session 
{

    public function get(?string $name = null, $type = 'success')
    {
        if(!in_array($type, ['success', 'danger', 'warning', 'info'])) {
            throw new \Exception('Vous pouvez utiliser les types "success", "danger", "warning" et "info"');
        }
        $flashbag = [];
        if(isset($_SESSION['_flashbag'])) {
            if($name === null) {
                $flashbag = $_SESSION['_flashbag'];
                unset($_SESSION['_flashbag']);
            }
            elseif(isset($_SESSION['_flashbag'][$type]) && isset($_SESSION['_flashbag'][$type][$name])) {
                $flashbag = $_SESSION['_flashbag'][$type][$name] ?? null;
                unset($_SESSION['_flashbag'][$type][$name]);
            }
        }

        return $flashbag;
    }

    public function set(string $name, $value = null, $type = 'success')
    {            
        if(!in_array($type, ['success', 'danger', 'warning', 'info'])) {
            throw new \Exception('Vous pouvez utiliser les types "success", "danger", "warning" et "info"');
        }
        $_SESSION['_flashbag'][$type][$name] = $value;
    }
}