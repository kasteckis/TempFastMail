<?php

namespace App\Controller\Admin;

use App\Entity\ReceivedEmail;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReceivedEmailCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReceivedEmail::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(),
            TextField::new('realFrom'),
            TextField::new('realTo'),
            TextField::new('subject'),
            TextField::new('fromName'),
            TextField::new('fromAddress'),
            TextField::new('hasAssignedTemporaryEmailBox'),
            TextField::new('toMultipleString')->hideOnIndex(),
            TextField::new('bccMultipleString')->hideOnIndex(),
            DateTimeField::new('createdAt'),
            TextField::new('html')->hideOnIndex(),
            TextField::new('metadataString')->hideOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewHtml = Action::new('viewHtml', 'View HTML', 'fas fa-file-alt')
            ->linkToUrl(function ($entity) {
                return $this->generateUrl('admin_received_email_view_html', [
                    'id' => $entity->getId(),
                ]);
            })
            ->setHtmlAttributes(['target' => '_blank']);

        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, $viewHtml)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    #[Route('/admin/received-email/{id}/view-html', name: 'admin_received_email_view_html')]
    public function viewHtml(ReceivedEmail $email): Response
    {
        return $this->render('admin/received_email/view_html.html.twig', [
            'html' => $email->getHtml()
        ]);
    }
}
