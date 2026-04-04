<?php

namespace App\Controller\Admin;

use App\Entity\Domain;
use App\Repository\ReceivedEmailRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DomainCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly ReceivedEmailRepository $receivedEmailRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Domain::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(),
            TextField::new('domain'),
            TextField::new('emailCount')
                ->onlyOnIndex()
                ->formatValue(function ($value, Domain $entity) {
                    return $this->receivedEmailRepository->countByDomain($entity->getDomain());
                }),
            DateTimeField::new('activeUntil'),
        ];
    }
}
