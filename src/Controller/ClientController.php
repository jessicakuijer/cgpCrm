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

        // Filtrer les utilisateurs en fonction du filtre sélectionné
        if ($filter === 'clients') {
            $clients = $entityManager->getRepository(User::class)->findBy(['client' => true]);
        } elseif ($filter === 'prospects') {
            $clients = $entityManager->getRepository(User::class)->findBy(['client' => false]);
        } else {
            $clients = $entityManager->getRepository(User::class)->findAll();
        }

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'filter' => $filter,
        ]);
    }
}
