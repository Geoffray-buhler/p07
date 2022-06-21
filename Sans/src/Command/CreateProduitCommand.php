<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-produit',
    description: 'Creates a new produit.',
    hidden: false,
    aliases: ['app:add-produit']
)]

class CreateProduitCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }


    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-produit';

    // the command description shown when running "php bin/console list"
    protected static $defaultDescription = 'Creates a new produit.';

    // ...
    protected function configure(): void
    {
        $this
            // If you don't like using the $defaultDescription static property,
            // you can also define the short description using this method:
            ->addArgument('Name', InputArgument::REQUIRED, 'The Name of the produit.')
            ->addArgument('Color', InputArgument::REQUIRED, 'The Color of the produit.')
            ->addArgument('Description', InputArgument::REQUIRED, 'The Description of the produit.')
            ->addArgument('Prices', InputArgument::REQUIRED, 'The Prices of the produit.')
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a produit...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->entityManager;

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Produit Creator',
            '============',
            '',
        ]);

        // the value returned by someMethod() can be an iterator (https://secure.php.net/iterator)
        // that generates and returns the messages with the 'yield' PHP keyword
        // $output->writeln($this->someMethod());

        // outputs a message followed by a "\n"

        // outputs a message without adding a "\n" at the end of the line
        
        $produit = new Produit;

        $produit->setName($input->getArgument('Name'));
        $produit->setColor($input->getArgument('Color'));
        $produit->setDescription($input->getArgument('Description'));
        $produit->setPrices($input->getArgument('Prices'));
        
        $output->writeln(['Ses infos sont bon ?']);
        $output->writeln([
            'Name : '.$input->getArgument('Name'),
            'Color : '.$input->getArgument('Color'),
            'Description : '.$input->getArgument('Description'),
            'Prices : '.$input->getArgument('Prices')
        ]);

        $em->persist($produit);
        $em->flush();

        $output->writeln('Produit successfully generated!');


        return Command::SUCCESS;
    }
}
