<?php

namespace Mdanter\Ecc\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;

class ListCurvesCommand extends Command
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('list-curves')
            ->setDescription('Lists all the available curves.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(CurveOidMapper::getNames());
    }
}
