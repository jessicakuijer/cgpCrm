<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class CsvExporter
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function exportToCsv(): Response
    {
        $data = $this->em->getRepository(User::class)->findAll();

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

        $handle = fopen('php://temp', 'r+');

        // Write UTF-8 BOM
        fwrite($handle, "\xEF\xBB\xBF");

        // Add the header of the CSV file
        fputcsv($handle, ['ID', 'Nom', 'Prénom', 'Téléphone', 'Date de naissance', 'Email', 'Adresse', 'Profession', 'Est un client', 'Recommandation', 'Commentaire'], ';');

        // Add the data queried from database
        foreach ($data as $row) {
            $csvRow = [
                $row->getId(), 
                $row->getNom(), 
                $row->getPrenom(), 
                $row->getTelephone(), 
                $row->getDatedenaissance() ? $row->getDatedenaissance()->format('Y-m-d') : null,
                $row->getEmail(), 
                $row->getAdresse(),
                $row->getProfession(),
                $row->isClient() ? 'Oui' : 'Non',
                $row->getRecommandation() ? $row->getRecommandation()->getNom() . ' ' . $row->getRecommandation()->getPrenom() : null,
                $row->getCommentaire()
            ];

            // Convert each element of the array to UTF-8
            $csvRow = array_map(function($value) {
                return mb_convert_encoding($value, 'UTF-8');
            }, $csvRow);

            fputcsv($handle, $csvRow, ';');
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $response->setContent($content);

        return $response;
    }

}
