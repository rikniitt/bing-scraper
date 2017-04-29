<?php

namespace BingScraper\Console\Command\Db;

use BingScraper\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Console extends Command
{
    protected function configure()
    {
        $this->setName('db:console')
             ->setDescription('Open database console');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $container = $this->getContainer();
        $database = $container['db.file'];

        $this->out->writeln('Opening database console.');

        $cmd = '/usr/bin/sqlite3 -column -header ' . $database;
        $this->out->writeln(" $ $cmd"); 

        $process = new Process($cmd);
        try {
            $process->setTty(true);
            $process->mustRun(function ($type, $buffer) {});
        } catch (ProcessFailedException $e) {
            $this->out->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
