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
        $this->rightsManager = new RightsManager();
        $this->_csrfToken = $this->session()->securityCsrfToken;

        $loader = new \Twig\Loader\FilesystemLoader($this->templatesDirectory);
        $this->templateEngine = new \Twig\Environment($loader);
        $this->templateEngine->addGlobal('rights', $this->rightsManager);
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
                'csrf_token'    => $this->generateCsrfToken(),
                'errors'        => $this->errors,
                'current_page'  => $_SERVER["PATH_INFO"]??'/'
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

    
    public function isGranted(...$roles)
    {
        if($this->rightsManager->isGranted(...$roles)) {
            return true;
        }
        $this->error401();
    }

    protected function isConnected()
    {
        return $this->rightsManager->isConnected();
    }

    protected function isConnectedRedirect(string $url)
    {
        if($this->isConnected() === true)
        {
            $this->redirectToRoute($url);
        }
    }

    protected function isNotConnectedRedirect(string $url)
    {
        if($this->isConnected() !== true) 
        {
            $this->redirectToRoute($url);
        }
    }

    public function pathToUrl(string $path, bool $absolute = false)
    {
        $url = '';
        if($absolute) {
            $url = $this->getUrl();
        } 
        return $url.str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('/','\\', $path)));
    }

    public function getUrl()
    {
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
        return $protocol.'://'.$_SERVER["SERVER_NAME"].($_SERVER["SERVER_PORT"] != 80 ? ':'.$_SERVER["SERVER_PORT"] : '');
    }

    private function error401()
    {
        header("HTTP/1.1 401 Unauthorized");
        if(file_exists($this->templatesDirectory.'/errors/error_401.html.twig')) {
            echo $this->render('errors/error_401');
        } else {
            echo '<h1>401 Unauthorized</h1>';
        }
        exit();
    }
}