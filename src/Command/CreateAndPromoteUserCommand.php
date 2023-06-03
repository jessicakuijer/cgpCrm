<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-and-promote-user',
    description: 'Create a new user and set it as admin',
)]
class CreateAndPromoteUserCommand extends Command
{
    protected static $defaultName = 'app:create-and-promote-user';

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'The email of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();

        // Run the user creation command
        $createCommand = $application->find('app:create-user');
        $createCommand->run(new ArrayInput([
            'command' => 'app:create-user',
            'email' => $input->getArgument('email'),
        ]), $output);

        // Run the user promotion command
        $promoteCommand = $application->find('app:promote-admin');
        $promoteCommand->run(new ArrayInput([
            'command' => 'app:promote-admin',
            'email' => $input->getArgument('email'),
        ]), $output);

        return Command::SUCCESS;
    }
}