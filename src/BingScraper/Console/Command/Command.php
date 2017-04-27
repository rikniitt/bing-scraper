<?php

namespace BingScraper\Console\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{
    protected $in;
    protected $out;

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $this->in = $in;
        $this->out = $out;

        $container = $this->getContainer();
        $container['logger']->debug('Execute ' . get_class($this) . '.');
    }

    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }
}
