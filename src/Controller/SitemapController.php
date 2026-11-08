<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SitemapController extends AbstractController
{
    public function __construct(
        private BlogRepository $blogRepository,
        #[Autowire(env: 'DEFAULT_URI')] private string $defaultUri,
    ) {
    }

    #[Route('/sitemap.xml', name: 'app_sitemap', methods: ['GET'])]
    public function sitemap(): Response
    {
        $blogs = $this->blogRepository->findBy([], ['createdAt' => 'DESC']);
        $latestBlogDate = $blogs[0]?->getCreatedAt();

        $urls = [
            ['loc' => $this->absoluteUrl('app_home'), 'lastmod' => $latestBlogDate],
            ['loc' => $this->absoluteUrl('app_blog'), 'lastmod' => $latestBlogDate],
            ['loc' => $this->absoluteUrl('app_contact'), 'lastmod' => null],
            ['loc' => $this->absoluteUrl('app_privacy_policy'), 'lastmod' => null],
        ];

        foreach ($blogs as $blog) {
            $urls[] = [
                'loc' => $this->absoluteUrl('app_view_single_blog', ['slug' => $blog->getSlug()]),
                'lastmod' => $blog->getCreatedAt(),
            ];
        }

        $response = $this->render('sitemap/sitemap.xml.twig', ['urls' => $urls]);
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }

    #[Route('/robots.txt', name: 'app_robots', methods: ['GET'])]
    public function robots(): Response
    {
        $response = $this->render('sitemap/robots.txt.twig', [
            'sitemapUrl' => $this->absoluteUrl('app_sitemap'),
        ]);
        $response->headers->set('Content-Type', 'text/plain; charset=UTF-8');
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }

    private function absoluteUrl(string $route, array $parameters = []): string
    {
        return rtrim($this->defaultUri, '/') . $this->generateUrl($route, $parameters);
    }
}
