<?php

namespace Yoop;

session_start();
abstract class AbstractController
{
    protected $errors = [];

    private $_csrfToken;

    private $_session;

    private $templateEngine;

    public function __construct() 
    {
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__, 4) . '/templates');
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
        return $this->templateEngine->render($view.'.html.twig', $vars);
    }

    protected function isSubmitted()
    {
        var_dump($_POST['csrf_token']);
        var_dump($this->_csrfToken);
        return (sizeof($_POST) > 0 && !empty($_POST['csrf_token']) && $_POST['csrf_token'] === $this->_csrfToken);
    }

    protected function session()
    {
        if($this->_session === null) {
            $this->_session = new Session();
        }
        return $this->_session;
    }
}
 