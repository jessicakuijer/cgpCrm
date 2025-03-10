<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CsvImporter
{
    private $em;
    private $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
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

            // Verify and constrain the data
            // Validate the email
            $emailConstraint = new Assert\Email();
            $emailErrors = $this->validator->validate(
                $csvRow['Email'],
                $emailConstraint
            );

            if (count($emailErrors) > 0) {
                throw new \Exception($emailErrors[0]->getMessage());
            }

            // Validate the number of children
            $childrenConstraint = new Assert\PositiveOrZero();
            $childrenErrors = $this->validator->validate(
                $csvRow['Enfants'],
                $childrenConstraint
            );

            if (count($childrenErrors) > 0) {
                throw new \Exception($childrenErrors[0]->getMessage());
            }

            // Validate the marital status
            $maritalConstraint = new Assert\Regex([
                // REGEX to allow only letters, spaces and accents
                'pattern' => '/^[a-zA-Z\x{00C0}-\x{00FF}\s]*$/u',
                'message' => 'Le statut marital ne peut contenir que des lettres et des espaces.'
            ]);

            $maritalErrors = $this->validator->validate(
                $csvRow['Statut Marital'],
                $maritalConstraint
            );

            if (count($maritalErrors) > 0) {
                throw new \Exception($maritalErrors[0]->getMessage());
            }

            // Set the data

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
            $user->setCivil($csvRow['Statut Marital'] ?? '');
            $user->setEnfants(isset($csvRow['Enfants']) ? (int)$csvRow['Enfants'] : null);
            $user->setClient(strtolower($csvRow['Est un client']) === 'oui' ? true : false);
            
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

            // Validate and persist the user
            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                throw new \Exception((string) $errors);
            }

            $this->em->persist($user);
        }

        fclose($handle);

        $this->em->flush();

        return true;
    }

}