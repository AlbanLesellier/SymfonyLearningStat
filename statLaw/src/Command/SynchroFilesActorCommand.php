<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:synchro-files-actor',
    description: 'Add a short description for your command',
)]
class SynchroFilesActorCommand extends Command
{
    public function __construct()
    {
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
        $pathToImport = "/workspaces/SymfonyLearningStat/statLaw/data/toImport/acteur";
        $pathImported = "/workspaces/SymfonyLearningStat/statLaw/data/imported/acteur";
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

        foreach ($io->progressIterate($files) as $file) {
      
          
            if(is_file($pathToImport."/".$file) && !is_file($pathImported."/".$file)) {
                $output->writeln($file);
                $content = file_get_contents($pathToImport."/".$file);
                $data = json_decode($content);
                file_put_contents($pathImported."/".$file.".txt",print_r($data,true));
                //$output->writeln(print_r($data,true));
                return Command::SUCCESS;
            } else {
                @unlink($pathToImport."/".$file);
            }
        
        }


        
        //$zip = ("https://www.data.gouv.fr/fr/datasets/r/5a3089b4-c59c-4e0b-a046-344168764cb7");
        //$ch = curl_init();
        //$source = "https://www.data.gouv.fr/fr/datasets/fichier-historique-des-deputes-et-de-leurs-mandats/#/resources/5a3089b4-c59c-4e0b-a046-344168764cb7";
        //curl_setopt($ch, CURLOPT_URL, $source);
        //$data = curl_exec ($ch);
        //curl_close ($ch);
        //var_dump($data);
    
        
        //var_dump($zip);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
