<?php

namespace BingScraper\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Pimple\Container;

class Application extends SymfonyApplication
{
    private $container;

    public function __construct(Container $container, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->container = $container;
        $container['logger']->debug('BingScraper\Console\Application created.');
    }

    public function getContainer()
    {
        return $this->container;
    }
}
