<?php

namespace BingScraper\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class Install extends Command
{
    protected function configure()
    {
        $this->setName('install')
             ->setDescription('Install the project.');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $this->out->writeln("Installing project.");

        $this->initDatabaseFile();
        $this->databaseMigrate();
    }

    private function initDatabaseFile()
    {
        $container = $this->getContainer();
        $databaseFile = $container['db.file'];
        
        if (file_exists($databaseFile)) {
            $this->out->writeln("Database file $databaseFile already exists.");
        } else {
            $this->out->writeln("Creating database file $databaseFile.");
            touch($databaseFile);
            $this->out->writeln(" $ touch $databaseFile");
        }
    }

    private function databaseMigrate()
    {
        $this->out->writeln("Running database migrations.");

        $migrate = $this->getApplication()->find('db:migrate');
        return $migrate->run(new ArrayInput([]), $this->out);
    }

}
