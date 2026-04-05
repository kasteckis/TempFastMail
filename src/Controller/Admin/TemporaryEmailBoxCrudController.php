<?php

namespace App\Controller\Admin;

use App\Entity\TemporaryEmailBox;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TemporaryEmailBoxCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TemporaryEmailBox::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(),
            TextField::new('email'),
            TextField::new('uuid'),
            TextField::new('creatorIp'),
            TextField::new('countryCode'),
            AssociationField::new('owner'),
            AssociationField::new('receivedEmails')
                ->hideOnForm(),
            DateTimeField::new('createdAt')->setDisabled(),
            DateTimeField::new('lastAccessedAt')->setDisabled(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $impersonateAction = Action::new('impersonate', 'Impersonate', 'fas fa-user-secret')
            ->linkToUrl(function (TemporaryEmailBox $emailBox): string {
                return '/?' . http_build_query([
                    'impersonate_email' => $emailBox->getEmail(),
                    'impersonate_uuid' => $emailBox->getUuid(),
                ]);
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $impersonateAction)
            ->add(Crud::PAGE_DETAIL, $impersonateAction);
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'DESC']);
    }
}
