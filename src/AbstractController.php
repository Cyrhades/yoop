<?php
 
 namespace Yoop;
  
 abstract class AbstractController
 {
    private $templateEngine;

    private $flashbag;

    public function __construct() 
    {
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__, 4) . '/templates');
        // Si la variable .env est active on peut utiliser le debug dans twig
        $this->templateEngine = new \Twig\Environment($loader, [
            'debug' => (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'dev'),
        ]);        
        $this->templateEngine->addExtension(new \Twig\Extension\DebugExtension());
        
        $this->flashbag = new FlashBag();
    }

    /**
     * Helper repository
     */
    protected function getRepository($name) 
    {
        $repoName = 'App\\Repository\\'.$name.'Repository';
        return new $repoName();
    }

    /**
     * Getter pour flashBag
     */
    protected function flash()
    {
        return $this->flashbag;
    }

    /**
     * Générer le rendu HTML avec Twig
     */
    protected function render($view, $vars = [])
    {
        if(isset($_SESSION['user'])) {
            if(!isset($vars['app']) || !is_array($vars['app'])) $vars['app'] = [];
            $vars['app']['user'] = $_SESSION['user'];
        }
        return $this->templateEngine->render($view.'.html.twig', $vars);
    }

    /**
     * Connexion de l'utilisateur
     */
    protected function connectUser(EntityInterface $user) {
        $_SESSION['user'] = $user;
    }

    /**
     * L'utilisateur connecté
     */
    protected function getUser(): ?EntityInterface {
        if(!empty($_SESSION['user'])) {
            return $_SESSION['user'];
        }
        return null;
    }
    
    
    /**
     * Connexion de l'utilisateur
     */
    protected function isAuthenticated() {
        return (isset($_SESSION['user']) && !empty($_SESSION['user']->getId()) );
    }

    /**
     * Gestion des redirections
     */
    protected function redirectToRoute($url)
    {
        header("location:$url");
        exit();
    }


    /**
     * Cette méthode me permet de retoruner un tableau 
     * associatif à partir d'une entity
     */
    protected function getEntity(EntityInterface $entity) 
    {
        if(is_object($entity)) {   
            $result = [];
            // Boucle sur les attribut de l'entiity
            foreach ((array) $entity as $key => $value)
            {
                // exemple de $key : "App\Entity\Customer firstName"
                // attributName recevra uniquement "firstName" par rapport à notre exemple
                $attributName = trim(substr($key, strrpos($key, chr(0))));
                // à partir de attributName on va créer le nom du getter (exemple : getFirstName)
                $getter = 'get'.ucfirst($attributName); 
                // si la méthode existe et que la valeur n'est pas null
                if(method_exists($entity, $getter) && is_null($value) === false) {
                    // on l'ajoute à notre tableau associatif
                    $result[$attributName] = call_user_func([$entity,$getter]);
                }
            }
            // retourne le tableau associatif créé à partir de l'entity
            return $result;
        }
        return; // retourne null
    }
}
 