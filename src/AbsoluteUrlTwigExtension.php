<?php

namespace Yoop;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AbsoluteUrlTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('absolute_url', [$this, 'absoluteUrl']),
        ];
    }

    public function absoluteUrl(string $path): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];
        
        // Ajouter le port uniquement s'il est non standard
        if (($protocol === "http://" && $port != 80) || ($protocol === "https://" && $port != 443)) {
            $host .= ':' . $port;
        }
        // ajout vérification des caracteres autorisés pour l'alias
        $proxyHttp = $_SERVER['HTTP_X_FORWARDED_PREFIX_PROXY'] ?? '';
        if (preg_match('/^[\/a-zA-Z0-9_-]+$/', $proxyHttp)) {
            $baseUrl = $_SERVER['HTTP_X_FORWARDED_PREFIX_PROXY'];
        } else {
            $baseUrl = $protocol.$host ?? '';
        }

        // Nettoyage du path s’il commence par un /
        $path = ltrim($path, '/');

        // Si HTTP_REFERER se termine par un slash, on ne rajoute pas un autre
        return rtrim($baseUrl, '/') . '/' . $path;
    }
}
