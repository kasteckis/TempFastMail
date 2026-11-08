<?php

namespace App\Extension\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        #[Autowire(env: 'DEFAULT_URI')] private string $defaultUri,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('domain', [$this, 'getDomain']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('site_url', [$this, 'getSiteUrl']),
        ];
    }

    public function getDomain(string $url): ?string
    {
        return parse_url($url, PHP_URL_HOST);
    }

    public function getSiteUrl(string $path = ''): string
    {
        return rtrim($this->defaultUri, '/') . $path;
    }
}
