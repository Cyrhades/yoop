<?php

namespace Yoop;

class ContentSecurityPolicy
{
    const NONCE_LENGTH = 32;

    private ?string $currentNonce = null;

    private bool $strictMode = true;

    private array $policies = [
        'default-src' => ["'self'"],
        'script-src'  => ["'self'"],
        'style-src'   => ["'self'"],
        'font-src'    => ["'self'"],
        'img-src'     => ["'self'"],
        'frame-src'   => ["'self'"],
        'connect-src' => ["'self'"],
    ];

    public function getCurrentNonce(): string
    {
        if ($this->currentNonce === null) {
            $this->currentNonce = $this->generateNonce();
        }
        return $this->currentNonce;
    }

    private function generateNonce(): string
    {
        return base64_encode(random_bytes(self::NONCE_LENGTH));
    }


    public function enableStrictMode(bool $strict = true): void
    {
        $this->strictMode = $strict;
    }


    public function addScriptSrc(array $urls): void
    {
        $this->addAuthorizedCSP('script-src', $urls);
    }

    public function addStyleSrc(array $urls): void
    {
        $this->addAuthorizedCSP('style-src', $urls);
    }

    public function addImgSrc(array $urls): void
    {
        $this->addAuthorizedCSP('img-src', $urls);
    }

    public function addFontSrc(array $urls): void
    {
        $this->addAuthorizedCSP('font-src', $urls);
    }

    public function addFrameSrc(array $urls): void
    {
        $this->addAuthorizedCSP('frame-src', $urls);
    }

    public function addConnectSrc(array $urls): void
    {
        $this->addAuthorizedCSP('connect-src', $urls);
    }

    public function addAuthorizedCSP(string $type, array $addresses): void
    {
        if (!isset($this->policies[$type])) {
            $this->policies[$type] = [];
        }

        foreach ($addresses as $address) {
            if (!$this->isValidSource($address)) {
                continue; // ignore valeurs dangereuses
            }

            if (!in_array($address, $this->policies[$type], true)) {
                $this->policies[$type][] = $address;
            }
        }
    }

    private function isValidSource(string $source): bool
    {
        // autorise keywords CSP
        $allowedKeywords = [
            "'self'",
            "'unsafe-inline'",
            "'unsafe-eval'",
            "data:",
            "blob:"
        ];

        if (in_array($source, $allowedKeywords, true)) {
            return true;
        }

        // bloque javascript:
        if (str_starts_with($source, 'javascript:')) {
            return false;
        }

        // autorise uniquement http/https
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            return str_starts_with($source, 'https://') || str_starts_with($source, 'http://');
        }

        return false;
    }


    private function buildCSPString(): string
    {
        $parts = [];

        foreach ($this->policies as $directive => $values) {

            // MODE STRICT
            if ($this->strictMode && in_array($directive, ['script-src', 'style-src'])) {
                // enlève unsafe-inline si strict
                $values = array_filter($values, fn($v) => $v !== "'unsafe-inline'");
            }

            // Ajout nonce automatique
            if (in_array($directive, ['script-src', 'style-src'])) {
                $values[] = "'nonce-" . $this->getCurrentNonce() . "'";
            }

            $values = array_unique($values);

            $parts[] = $directive . ' ' . implode(' ', $values);
        }

        return implode('; ', $parts) . ';';
    }

    public function getCSP(): array
    {
        return [
            'Content-Security-Policy'   => $this->buildCSPString(),
            'X-Frame-Options'           => 'SAMEORIGIN',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
            'X-Content-Type-Options'    => 'nosniff',
            'Referrer-Policy'           => 'strict-origin-when-cross-origin'
        ];
    }
}