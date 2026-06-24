<?php

namespace Yoop;

class Kernel
{
    private $router;

    private $lang;

    private $csp;

    public function __construct()
    {
        if (!defined("ROOT_DIR")) {
            define("ROOT_DIR", dirname(__DIR__, 4));
        }

        StaticDotEnv::load(ROOT_DIR . '/.env');

        $this->router = new Router;
        $this->lang = new Language($_ENV["LANGUAGE"] ?? null);
        $this->csp = new ContentSecurityPolicy();
    }

    public function contentSecurityPolicy()
    {
        return $this->csp;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function __(string $trad, array $params = [])
    {
        return $this->lang->get($trad, $params);
    }
}
