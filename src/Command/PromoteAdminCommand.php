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
    name: 'app:promote-admin',
    description: 'set admin role to a user',
)]
class PromoteAdminCommand extends Command
{
    protected static $defaultName = 'app:promote-admin';

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

        // Get the user you want to promote.
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $input->getArgument('email')]);

        if (!$user) {
            $io->error('User not found.');
            return Command::FAILURE;
        }

        // Set the roles for the user.
        $roles = $user->getRoles();
        $roles[] = 'ROLE_ADMIN';
        $user->setRoles($roles);

        // Set the password for the user.
        $plaintextPassword = $this->parameterBag->get('ADMIN_PLAIN_PASSWORD');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        // Save the changes.
        $this->entityManager->flush();

        $io->success('User promoted to admin.');

        return Command::SUCCESS;
    }
}
