<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom')->setLabel('Nom'),
            TextField::new('prenom')->setLabel('Prénom'),
            TextField::new('telephone')->setLabel('Téléphone'),
            DateField::new('datedenaissance')->setLabel('Date de Naissance'),
            EmailField::new('email')->setLabel('Email'),
            TextField::new('adresse')->setLabel('Adresse'),
            TextField::new('profession')->setLabel('Profession'),
            BooleanField::new('client')->setLabel('Client'),
            AssociationField::new('recommandation')->setLabel('Recommandation'),
            TextareaField::new('commentaire')->setLabel('Commentaire'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Client') // Utilisé pour un seul client (ex: Édition du client #123)
            ->setEntityLabelInPlural('Liste des clients'); // Utilisé pour un groupe de clients (ex: Liste des clients)
    }
}

