<?php

namespace BingScraper\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Repl extends Command
{
    protected function configure()
    {
        $this->setName('repl')
             ->setDescription('Open PHP REPL.');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $this->out->writeln('Starting PsySH. Access pimple container with $container.');

        $cmd = '/usr/bin/php ' . PROJECT_DIR . '/vendor/bin/psysh ' . PROJECT_DIR . '/config/bootstrap.php';
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
