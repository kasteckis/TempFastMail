<?php

namespace App\Controller\Admin;

use App\Entity\ReceivedEmail;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
            TextField::new('bccName'),
            TextField::new('bccAddress'),
            ArrayField::new('toMultiple'),
            TextField::new('html'),
            ArrayField::new('metadata'),
            DateTimeField::new('createdAt'),
        ];
    }
}
