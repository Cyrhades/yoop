<?php

namespace Yoop;

session_start();
abstract class AbstractController
{
    protected $errors = [];

    private $_csrfToken;

    private $_session;

    private $_flashbag;

    private $templateEngine;

    protected $templatesDirectory;

    public function __construct() 
    {
        $this->templatesDirectory = dirname(__DIR__, 4) . '/templates';
        $loader = new \Twig\Loader\FilesystemLoader($this->templatesDirectory);
        $this->templateEngine = new \Twig\Environment($loader);
        $this->_csrfToken = $this->session()->securityCsrfToken;
    }

    protected function generateCsrfToken()
    {
        $this->session()->securityCsrfToken = sha1(uniqid('csrf_token'));
        $this->_csrfToken = $this->session()->securityCsrfToken;
        return $this->_csrfToken;
    }

    protected function render($view, $vars = [])
    {
        return $this->templateEngine->render(
            $view.'.html.twig', 
            array_merge(
                $vars,
                $this->session()->get()
            )
        );
    }

    protected function isSubmitted()
    {
        if(sizeof($_POST) > 0 && !empty($_POST['csrf_token']) && $_POST['csrf_token'] === $this->_csrfToken) {
            return true;
        }

        $this->errors = 'Erreur CSRF token !';
        return false;
    }

    protected function session()
    {
        if($this->_session === null) {
            $this->_session = new Session();
        }
        return $this->_session;
    }

    protected function flashbag()
    {
        if($this->_flashbag === null) {
            $this->_flashbag = new FlashBag();
        }
        return $this->_flashbag;
    }

    protected function redirectToRoute(string $url)
    {
        header("location:".$url);
        exit();
    }
}
 