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
        $baseUrl = $_SERVER['HTTP_X_FORWARDED_PREFIX_PROXY'] ?? $_SERVER['HTTP_REFERER'] ?? '';

        // Nettoyage du path s’il commence par un /
        $path = ltrim($path, '/');

        // Si HTTP_REFERER se termine par un slash, on ne rajoute pas un autre
        return rtrim($baseUrl, '/') . '/' . $path;
    }
}