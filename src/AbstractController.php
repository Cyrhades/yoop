<?php

namespace Yoop;

abstract class AbstractController
{
    protected $errors = [];

    protected $templatesDirectory;

    protected $uri;

    protected $requestMethod;

    private $_csrfToken;

    private $_session;

    private $_flashbag;

    private $templateEngine;


    public function __construct($request = []) 
    {
        $this->requestMethod = $request['requestMethod'];
        $this->uri = $request['uri'];

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
        $data =  array_merge(
            $vars,
            $this->session()->get(),
            [
                'csrf_token' => $this->generateCsrfToken(),
                'errors'     => $this->errors
            ]
        );

        if($this->requestMethod == 'POST') {
            $data = array_merge($_POST, $data);
        }
        
        return $this->templateEngine->render($view.'.html.twig', $data);
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
        header("Location: ".$url);
        exit();
    }
}