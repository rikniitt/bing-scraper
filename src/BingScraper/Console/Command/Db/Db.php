<?php

namespace BingScraper\Console\Command\Db;

use BingScraper\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class Db extends Command
{
    protected function configure()
    {
        $this->setName('db')
             ->setHidden(true);
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $dbConsole = $this->getApplication()->find('db:console');
        return $dbConsole->run(new ArrayInput([]), $out);
    }
}
