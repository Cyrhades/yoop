<?php

namespace Yoop;

class Flashbag extends Session
{
    /**
 	 * Enregistrement d'un message
 	 *
 	 * @param string message à afficher
 	 * @param string type success | warning | error
 	 */
    public function set(string $message, string $type = 'success'):void
    {
        $_SESSION['flashbag'] = [
            'message' => $message,
            'type'  => $type
        ];
    }

    /**
 	 * Retourne le message et le type de message se trouvant dans
 	 * la session flash (tableau vide si pas de message)
 	 *
 	 * @return array
 	 */
    public function get():array
    {
        if (!empty($_SESSION['flashbag']) && is_array($_SESSION['flashbag'])) {
            $return = $_SESSION['flashbag'];
            // Le principe des flashbag étant qu'elles sont utilisées 
            // qu'une seule fois donc on supprime pendant le Get
            unset($_SESSION['flashbag']);
            return $return;
        }
        return [];
    }
}