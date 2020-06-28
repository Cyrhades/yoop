<?php

namespace Yoop;

class Cache
{

    private $cacheDirectory;

    public function __construct()
    {
        $this->cacheDirectory = dirname(__DIR__, 4) . '/var/cache/';
    }

    public function getInfosCache(?string $key = null)
    {
        $HumanSizes = [
            ["UNIT" => "To", "VALUE" => pow(1024, 4)],
            ["UNIT" => "Go", "VALUE" => pow(1024, 3)],
            ["UNIT" => "Mo", "VALUE" => pow(1024, 2)],
            ["UNIT" => "Ko", "VALUE" => 1024],
            ["UNIT" => "octet(s)",  "VALUE" => 1],
        ];

        if($key !== null) {
            $files = glob($this->cacheDirectory.$key.'_*');
        }
        else {
            $files = glob($this->cacheDirectory.'*');    
        }

        foreach($files as $file) {
            $key = str_replace( $this->cacheDirectory, '',substr($file, 0, strrpos($file, '_')));
            $expiration = substr($file, strrpos($file, '_')+1);
            $size = filesize($file);
            foreach($HumanSizes as $HumanSize)
            {
                if($size >= $HumanSize["VALUE"]) {
                    $size = str_replace(".", "," , strval(round(($size/$HumanSize["VALUE"]), 2)))." ".$HumanSize["UNIT"];
                    break;
                }
            }
            $data[] = [
                'key'           => $key,
                'expiration'    => date("d/m/Y H:i:s", $expiration),
                'size'          => $size
            ];
        }

        return $data;
    }

    /**
     * Retourne une donnée en cache et l'enregistresi nécessaire
     * 
     * @param string $key 
     * @param callable $data
     * @param int $expiration durée de vie du cache en secondes default : 1h (3600 secondes)
     */
    public function get(string $key, callable $data, int $expiration = 3600)
    {
        $content = $this->getItem($key);
        
        if($content === null) {
            $data = \call_user_func($data);
            if(!empty($data)) {
               file_put_contents($this->cacheDirectory.$key.'_'.(time()+$expiration), $data);
               $content = $this->getItem($key);
            }
        }

        return $content;
    }

    public function getItem(string $key)
    {
        $file = glob($this->cacheDirectory.$key.'_*');
        
        if(isset($file[0])) {

            $expiration = substr($file[0], strrpos($file[0], '_')+1);
            if(time() > $expiration) {
                unlink($file[0]);
            }
            elseif(file_exists($file[0])) {
                return file_get_contents($file[0]);
            }
        }
        return null;
    }

    public function getItems(array $keys):\Traversable
    {
        $items = [];
        foreach($keys as $key) { 
            $items[$ey] = $this->getItem($key);
        }
        return $items;
    }

    public function clear()
    {
        $files = glob($this->cacheDirectory.'*');
        foreach($files as $file) { 
            unlink($file);
        }
    }

    public function deleteItem($key)
    {
        $return = true;
        $files = glob($this->cacheDirectory.$key.'_*');
        foreach($files as $file) { 
            if(unlink($file) !== false) {
                $return = false;
            }
        }
        return $return;
    }

    public function deleteItems(array $keys)
    {
        $return = true;
        foreach($keys as $key) { 
            if($this->deleteItem($key) !== true) {
                $return = false;
            }
        }
        return $return;
    }
    
}