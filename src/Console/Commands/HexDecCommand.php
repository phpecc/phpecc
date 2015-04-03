<?php

namespace Mdanter\Ecc\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;

class HexDecCommand extends Command
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('hexdec')
            ->addArgument('hex', InputArgument::REQUIRED, 'Hex value');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hex = str_replace(' ', '', $input->getArgument('hex'));
        $adapter = MathAdapterFactory::getAdapter();

        $output->writeln($adapter->hexDec($hex));
    }
}
