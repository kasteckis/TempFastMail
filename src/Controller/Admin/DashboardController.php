<?php

namespace App\Controller\Admin;

use App\Entity\Blog;
use App\Entity\Domain;
use App\Entity\ReceivedEmail;
use App\Entity\TemporaryEmailBox;
use App\Entity\User;
use App\Repository\DomainRepository;
use App\Repository\ReceivedEmailRepository;
use App\Repository\TemporaryEmailBoxRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private DomainRepository $domainRepository,
        private TemporaryEmailBoxRepository $temporaryEmailBoxRepository,
        private ReceivedEmailRepository $receivedEmailRepository,
    ) {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'activeDomainsCount' => $this->domainRepository->countOfActiveDomains(),
            'temporaryEmailBoxCount' => $this->temporaryEmailBoxRepository->count(),

            'receivedEmailsWithEmailBoxCount' => $this->receivedEmailRepository->countWithTemporaryEmailBox(),
            'receivedEmailsWithoutEmailBoxCount' => $this->receivedEmailRepository->countWithoutTemporaryEmailBox(),

            'readEmails' => $this->receivedEmailRepository->countReadEmails(),
            'unreadEmails' => $this->receivedEmailRepository->countUnreadEmails(),

            'oldestReceivedEmailAt' => $this->receivedEmailRepository->findOneBy([], ['createdAt' => 'ASC']),
            'newestReceivedEmailAt' => $this->receivedEmailRepository->findOneBy([], ['createdAt' => 'DESC']),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setFaviconPath('favicon.png')
            ->generateRelativeUrls()
            ->setTitle('Temporary Fast Mail Admin Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fas fa-home');
        yield MenuItem::linkToCrud('Domains', 'fas fa-globe', Domain::class);
        yield MenuItem::linkToCrud('Blog Posts', 'fas fa-pen', Blog::class);
        yield MenuItem::section('Users data');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Temporary Email Boxes', 'fas fa-paper-plane', TemporaryEmailBox::class);
        yield MenuItem::linkToCrud('Received Emails', 'fas fa-envelope', ReceivedEmail::class);
    }
}
