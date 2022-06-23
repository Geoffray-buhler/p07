<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
    hidden: false,
    aliases: ['app:add-user']
)]

class CreateUserCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }


    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-user';

    // the command description shown when running "php bin/console list"
    protected static $defaultDescription = 'Creates a new user.';

    // ...
    protected function configure(): void
    {
        $this
            // If you don't like using the $defaultDescription static property,
            // you can also define the short description using this method:
            // ->setDescription('...')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityManager = $this->entityManager;


        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);

        // the value returned by someMethod() can be an iterator (https://secure.php.net/iterator)
        // that generates and returns the messages with the 'yield' PHP keyword
        // $output->writeln($this->someMethod());

        // outputs a message followed by a "\n"

        // outputs a message without adding a "\n" at the end of the line
        
        $user = new User;
        $hashedPsw = password_hash($input->getArgument('password'),'2y');
       
        $user->setPassword($hashedPsw);
        $user->setUsername($input->getArgument('username'));
        $user->setEmail($input->getArgument('email'));
        
        $entityManager->persist($user);
        $entityManager->flush();

        $output->writeln('User successfully generated!');

        return Command::SUCCESS;
    }
}
