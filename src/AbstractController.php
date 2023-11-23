<?php
 
 namespace Yoop;
  
 abstract class AbstractController
 {
    private $templateEngine;

    private $flashbag;

    public function __construct() 
    {
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__, 4) . '/templates');
        $this->templateEngine = new \Twig\Environment($loader);
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
}
 