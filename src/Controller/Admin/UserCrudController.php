<?php

namespace App\Controller\Admin;

use IntlChar;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class UserCrudController extends AbstractCrudController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        $this->addFlash('success', 'L\'utilisateur a été créé avec succès !');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);

        $this->addFlash('success', 'L\'utilisateur a été mis à jour avec succès !');
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);

        $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès !');
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
            ChoiceField::new('civil')
                ->setLabel('Statut Marital')
                ->setChoices($this->getMaritalStatusChoices()),
            IntegerField::new('enfants')->setLabel('Nombre d\'enfants'),
            BooleanField::new('client')->setLabel('Client'),
            AssociationField::new('recommandation')->setLabel('Recommandation')->setFormTypeOptions([
                'placeholder' => 'Sélectionner une recommandation',
            ]),
            TextareaField::new('commentaire')->setLabel('Commentaire'),
        ];
    }

    private function getMaritalStatusChoices(): array
    {
        // Récupérer tous les statuts maritaux distincts dans la base de données
        $query = $this->entityManager->createQuery('SELECT DISTINCT u.Civil FROM App\Entity\User u WHERE u.Civil IS NOT NULL');
        $statuses = $query->getResult();

        // Transformer le tableau de résultats en format approprié pour setChoices
        $choices = [];
        foreach ($statuses as $status) {
            $choices[$status['Civil']] = $status['Civil'];
        }

        return $choices;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Client') // Utilisé pour un seul client (ex: Édition du client #123)
            ->setEntityLabelInPlural('Liste des clients'); // Utilisé pour un groupe de clients (ex: Liste des clients)
    }
}

