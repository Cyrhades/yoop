<?php

namespace Yoop;

class StaticDotEnv
{
    static public function load(string $path)
    {
        if(file_exists($path) && is_readable($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {  continue; }
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                if (!array_key_exists($name, $_ENV))  $_ENV[$name] = trim($value);
            }
        }
    }
}