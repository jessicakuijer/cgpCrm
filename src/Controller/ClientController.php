<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ClientController extends AbstractController
{
    #[Route('/clients', name: 'app_clients')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $filter = $request->query->get('filter', 'all');
        $search = $request->query->get('search', '');

        // Créer un QueryBuilder
        $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('u');
        
        // Appliquer le filtre de type (client/prospect)
        if ($filter === 'clients') {
            $queryBuilder->andWhere('u.client = :client')
                ->setParameter('client', true);
        } elseif ($filter === 'prospects') {
            $queryBuilder->andWhere('u.client = :client')
                ->setParameter('client', false);
        }
        
        // Appliquer la recherche si un terme est fourni
        if (!empty($search)) {
            $queryBuilder->andWhere('u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search OR u.profession LIKE :search OR u.adresse LIKE :search OR u.telephone LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        
        // Exécuter la requête
        $clients = $queryBuilder->getQuery()->getResult();

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'filter' => $filter,
            'search' => $search,
        ]);
    }
}
