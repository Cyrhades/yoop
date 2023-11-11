<?php

namespace Yoop;

use FastRoute;

class Router 
{
    private $routes;

    private $dispatcher;

    public function load($routes)
    {
        $routeFileName = dirname(__DIR__,4).DIRECTORY_SEPARATOR.trim($routes,DIRECTORY_SEPARATOR);
        if(file_exists($routeFileName)) {
            $this->routes = include $routeFileName;
        } else { 
            throw new \Exception("Le fichier route n'a pas été trouvé. créez le fichier /app/routes.php");
        }
    }

    public function run($requestMethod, $uri)
    {
        $dispatcher = FastRoute\simpleDispatcher($this->routes);
            
        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        
        $routeInfo = $dispatcher->dispatch($requestMethod, rawurldecode($uri));
        if($routeInfo[0] == FastRoute\Dispatcher::FOUND) {
            // Je vérifie si mon parametre est une chaine de caractere
            if(is_string($routeInfo[1])) {
                // si dans la chaine reçu on trouve les ::
                if(strpos($routeInfo[1], '::') !== false) {
                    //on coupe sur l'operateur de resolution de portée (::)
                    // qui est symbolique ici dans notre chaine de caractere.
                    $route = explode('::', $routeInfo[1]);
                    $method = [new $route[0], $route[1]];
                } else {
                    // sinon c'est directement la chaine qui nous interesse
                    $method = $routeInfo[1];
                }
            }
            // dans le cas ou c'est appelable (closure (fonction anonyme) par exemple)
            elseif(is_callable($routeInfo[1])) {
                $method = $routeInfo[1];
            }
            // on execute avec call_user_func_array
            echo call_user_func_array($method, $routeInfo[2]); 
        }        
        elseif($routeInfo[0] == FastRoute\Dispatcher::NOT_FOUND){
            header("HTTP/1.0 404 Not Found");
            if(method_exists('\App\Controller\ErrorController','print_404')) {
                echo call_user_func([new \App\Controller\ErrorController, 'print_404']);
            } else {
                echo '<h1>404 Not Found</h1>';
                exit();
            }
        }
        elseif($routeInfo[0] == FastRoute\Dispatcher::METHOD_NOT_ALLOWED){
            header("HTTP/1.0 405 Method Not Allowed");  
            if(method_exists('\App\Controller\ErrorController','print_405')) {
                echo call_user_func([new \App\Controller\ErrorController, 'print_405']);
            } else {
                echo '<h1>405 Method Not Allowed</h1>';
                exit();
            }
        }
    }
}