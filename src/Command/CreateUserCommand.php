<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new user',
)]
class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    private $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private ParameterBagInterface $parameterBag;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->parameterBag = $parameterBag;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'The email of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User();
        $user->setEmail($input->getArgument('email'));
        $user->setNom('Nom'); // Change this to whatever name you want
        $user->setPrenom('Prénom'); // Change this to whatever name you want
        $user->setTelephone('0606060606');//change this fake number after using the command
        $user->setDatedenaissance(new \DateTime('1975-12-18'));
        $user->setAdresse('adresse à modifier');
        $user->setProfession('profession à modifier');
        $user->setClient(0);
        $user->setCommentaire('client initialisé par la commande');
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $this->parameterBag->get('USER_PLAIN_PASSWORD')
            )
        );
        $user->setEnfants(0);
        $user->setCivil('statut marital à changer');

        // Here set the other required fields of the user, like the username, etc...

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('User created.');

        return Command::SUCCESS;
    }

}
