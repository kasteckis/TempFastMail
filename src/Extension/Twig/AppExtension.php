<?php

namespace App\Extension\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('domain', [$this, 'getDomain']),
        ];
    }

    public function getDomain(string $url): ?string
    {
        return parse_url($url, PHP_URL_HOST);
    }
}
