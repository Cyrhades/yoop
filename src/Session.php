<?php

namespace Yoop;

class Session 
{
    public function __construct()
    {
		$bStatut = false;
	    if ( php_sapi_name() !== 'cli' ) {
	        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
	            $bStatut = (session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE);
	        } else {
	            $bStatut = (session_id() === '' ? FALSE : TRUE);
	        }
	    }
    
    	if ($bStatut === FALSE) session_start();
    }



    public function destroy(?string $name = null)
    {
        if($name === null) {
            unset($_SESSION);
        }
        elseif(isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

    public function get(?string $name = null)
    {
        if($name === '_flashbag') { 
            return (new FlashBag)->get();
        }
        elseif($name === null) {
            $session = array_merge($_SESSION, (new FlashBag)->get());
            return $session;
        }
       
        return $_SESSION[$name] ?? null;
    }

    public function __get(string $name)
    {
        if($name === '_flashbag') { 
           return (new FlashBag)->get();
        }
        return $_SESSION[$name] ?? null;
    }

    public function __set(string $name, $value = null)
    {            
        $_SESSION[$name] = $value;
    }
}