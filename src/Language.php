<?php

namespace Yoop;

class Language {
    private $traductions = [];

    public function __construct($lang = "fr_FR") {
        $langFile = ROOT_DIR . "/i18n/{$lang}.php";
        if (file_exists($langFile)) {
            $this->traductions = include($langFile);
        } 
        // default language FR
        elseif(file_exists(ROOT_DIR . "/i18n/fr_FR.php")) {
            $this->traductions = include(ROOT_DIR . "/i18n/fr_FR.php");
        }
        // default language en
        elseif(file_exists( ROOT_DIR . "/i18n/en_US.php")) {
            $this->traductions = include( ROOT_DIR . "/i18n/en_US.php");
        } 
    }

    public function get(string $trad, array $params = [])
    {
        $message = null;

        // Vérifie d'abord si la clé complète existe directement
        if (isset($this->traductions[$trad])) {
            $message = $this->traductions[$trad];
        } else {
            // Sinon on tente un accès imbriqué via les points
            $keys = explode('.', $trad);

            $current = $this->traductions;
            $found = true;

            foreach ($keys as $key) {
                if (is_array($current) && isset($current[$key])) {
                    $current = $current[$key];
                } else {
                    $found = false;
                    break;
                }
            }

            if ($found && is_string($current)) {
                $message = $current;
            }
        }

        // Fallback si aucune traduction trouvée
        if ($message === null) {
            $message = $trad;
        }

        // Remplacement des placeholders
        foreach ($params as $placeholder => $value) {
            $message = str_replace('{{' . $placeholder . '}}', $value, $message);
        }

        return $message;
    }
}