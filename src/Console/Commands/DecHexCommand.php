<?php

namespace Mdanter\Ecc\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;

class DecHexCommand extends Command
{
    protected function configure()
    {
        $this->setName('dechex')
            ->addArgument('dec', InputArgument::REQUIRED, 'Decimal value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hex = $input->getArgument('dec');
        $adapter = MathAdapterFactory::getAdapter();

        $output->writeln($adapter->decHex($hex));
    }
}
