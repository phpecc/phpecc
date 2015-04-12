<?php

namespace Mdanter\Ecc\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MessageSignCommand extends AbstractCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('sign')
            ->addOption('keyfile', null, InputOption::VALUE_REQUIRED)
            ->addOption(
                'in',
                null,
                InputOption::VALUE_OPTIONAL,
                'Input format (der or pem). Defaults to pem.',
                'pem'
            )
            ->setDescription('Calculate a signature for a file.');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("We wont really do anything!");
        $parser = $this->getPrivateKeySerializer($input, 'in');
        $loader = $this->getLoader($input, 'in');

        $data = $this->getPrivateKeyData($input, $loader, 'keyfile', 'data');

        $key = $parser->parse($data);

    }
}
