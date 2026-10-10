<?php

namespace App\Controller;

use App\DTO\Response\DomainResponseDto;
use App\Entity\User;
use App\Repository\BlogRepository;
use App\Repository\DomainRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private BlogRepository $blogRepository,
        private DomainRepository $domainRepository,
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Always a strict boolean: anonymous and regular users are false.
        $isPremium = $this->isGranted(User::ROLE_PREMIUM);

        // The domain list (which includes premium domains) is only ever exposed to premium users.
        $domains = [];
        if ($isPremium) {
            $domains = array_map(DomainResponseDto::fromEntity(...), $this->domainRepository->findActiveDomains());
        }

        return $this->render('home/index.html.twig', [
            'blogs' => $this->blogRepository->findBy([], ['createdAt' => 'DESC'], 4),
            'isPremium' => $isPremium,
            'domains' => $domains,
        ]);
    }
}
