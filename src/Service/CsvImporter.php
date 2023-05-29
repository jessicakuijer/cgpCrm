<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvImporter
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function importFromCsv(UploadedFile $file): bool
    {
        $handle = fopen($file->getRealPath(), 'r');

        // Check the UTF-8 BOM and remove it if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Read the header of the CSV file
        $header = fgetcsv($handle, 0, ',');

        // Read the data from the CSV file
        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            $csvRow = array_combine($header, $data);

            $user = new User();
            $user->setNom($csvRow['Nom'] ?? '');
            $user->setPrenom($csvRow['Prenom'] ?? '');
            $user->setTelephone($csvRow['Telephone'] ?? '');

            $date = DateTime::createFromFormat('d-m-Y', $csvRow['Date de naissance']);
            if ($date !== false) {
                $user->setDatedenaissance($date);
            } else {
                // Set default birth date if the date cannot be converted
                $user->setDatedenaissance(new DateTime('1970-01-01'));
            }

            $user->setEmail($csvRow['Email'] ?? '');
            $user->setAdresse($csvRow['Adresse'] ?? '');
            $user->setProfession($csvRow['Profession'] ?? '');
            $user->setClient($csvRow['Est un client'] === 'Oui' ? true : false);
            
            if (isset($csvRow['Recommandation'])) {
                $recommandations = explode(',', $csvRow['Recommandation']);
                foreach ($recommandations as $recommandation) {
                    // Séparer le nom et le prénom
                    $recommandationParts = explode(' ', trim($recommandation));
                    $nom = $recommandationParts[0] ?? null;
                    $prenom = $recommandationParts[1] ?? null;
            
                    if ($prenom && $nom) {
                        // Rechercher l'utilisateur par nom et prénom
                        $recommandationUser = $this->em->getRepository(User::class)->findOneBy([
                            'prenom' => $prenom,
                            'nom' => $nom
                        ]);
            
                        // Si l'utilisateur existe, ajouter à la recommandation de l'utilisateur actuel
                        if ($recommandationUser) {
                            $user->setRecommandation($recommandationUser);
                        }
                    }
                }
            }
            
            

            $user->setCommentaire($csvRow['Commentaire'] ?? '');

            $this->em->persist($user);
        }

        fclose($handle);

        $this->em->flush();

        return true;
    }
}
