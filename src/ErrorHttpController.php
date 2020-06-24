<?php

namespace Yoop;

class ErrorHttpController extends AbstractController
{
    public function print_404() 
    {
        header("HTTP/1.0 404 Not Found");
        if(file_exists($this->templatesDirectory.'/errors/error_404.html.twig')) {
            return $this->render('errors/error_404');
        } else {
            return '<h1>404 Not Found</h1>';
        }
    }

    public function print_405() 
    {
        header("HTTP/1.0 405 Method Not Allowed");
        if(file_exists($this->templatesDirectory.'/errors/error_405.html.twig')) {
            return $this->render('errors/error_405');
        } else {
            return '<h1>405 Method Not Allowed</h1>';
        }
    }
}