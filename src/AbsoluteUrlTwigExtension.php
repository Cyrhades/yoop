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

        $baseUrl = $_SERVER['HTTP_X_FORWARDED_PREFIX_PROXY'] ?? $protocol.$host ?? '';

        // Nettoyage du path s’il commence par un /
        $path = ltrim($path, '/');

        // Si HTTP_REFERER se termine par un slash, on ne rajoute pas un autre
        return rtrim($baseUrl, '/') . '/' . $path;
    }
}