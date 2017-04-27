<?php

namespace BingScraper\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorld extends Command
{
    protected function configure()
    {
        $this->setName('hello-world')
             ->setDescription('Test console application')
             ->addArgument('to-who', InputArgument::OPTIONAL, 'To who?');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $who = $in->getArgument('to-who');

        if (!$who) {
            $who = 'world';
        }

        $message = 'Hello ' . $who . '!';

        $out->writeln($message);
        $this->getContainer()['logger']->info($message);
    }

}
