<?php

namespace BingScraper\Console\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }
}
