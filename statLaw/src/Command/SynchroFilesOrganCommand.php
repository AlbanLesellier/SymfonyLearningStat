<?php

namespace App\Command;

use App\Entity\Organ;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:synchro-files-organ',
    description: 'Add a short description for your command',
)]
class SynchroFilesOrganCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
        $pathToImport = "./data/toImport/organe";
        $pathImported = "./data/imported/organe";
        if(!is_dir($pathToImport)) {
            $io->error('To import directory doesn\'t exist');
            return Command::FAILURE;
        }
        if(!is_dir($pathImported)) {
            $io->error('Imprted directory doesn\'t exist');
            return Command::FAILURE;
        }

        $folder = opendir($pathToImport);
        $files = scandir($pathToImport);
       
        $entityManager = $this->entityManager;
        $repoOrgan = $entityManager->getRepository('Organ');
        foreach ($io->progressIterate($files) as $file) {
           
            if(is_file($pathToImport."/".$file) && !is_file($pathImported."/".$file)) {
                $output->writeln($file);
                $content = file_get_contents($pathToImport."/".$file);
                $data = json_decode($content);
                if(is_object($data)) {
                    $organ = new Organ();
                    $organ->setUid($data->uid);
                    $organ->setCodeType($data->codeType);
                    $organ->setLabel($data->libelle);
                    $organ->setShortLabel($data->libelleAbrege);
                    $organ->setStartDate(new \DateTime($data->viMoDe->dateDebut));
                    if($data->viMoDe->dateFin) {
                        $organ->setEndDate(new \DateTime($data->viMoDe->dateFin));
                    }
                    if($data->organeParent) {
                        $organParent = $repoOrgan->findOneBy(['uid' => $data->organeParent]);
                        $organ->setParentOrgan($organParent);
                    }
                    $entityManager->persist($organ);
                }
                
                file_put_contents($pathImported."/".$file.".txt",print_r($data,true));
                //$output->writeln(print_r($data,true));
                return Command::SUCCESS;
            } else {
                @unlink($pathToImport."/".$file);
            }
        
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
