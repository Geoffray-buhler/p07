<?php
// src/Command/HashPswCommand.php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:hash-psw',
    description: 'Hash your psw.',
    hidden: false,
    aliases: ['app:hash-psw']
)]

class PswHashCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }


    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:hash-psw';

    // the command description shown when running "php bin/console list"
    protected static $defaultDescription = 'Hash your psw';

    // ...
    protected function configure(): void
    {
        $this
            // If you don't like using the $defaultDescription static property,
            // you can also define the short description using this method:
            ->addArgument('Psw', InputArgument::REQUIRED, 'Psw hashed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Hash psw',
            '============',
        ]);

        // the value returned by someMethod() can be an iterator (https://secure.php.net/iterator)
        // that generates and returns the messages with the 'yield' PHP keyword
        // $output->writeln($this->someMethod());

        // outputs a message followed by a "\n"

        // outputs a message without adding a "\n" at the end of the line
        $options = [
            'cost' => 13,
        ];
        
        $output->writeln([
            'Psw : '.$input->getArgument('Psw'),
            'Psw hashed : '.password_hash($input->getArgument('Psw'), PASSWORD_DEFAULT,$options),
        ]);

        $output->writeln('Psw hashed');

        return Command::SUCCESS;
    }
}
