<?php

namespace Mdanter\Ecc\Console\Commands;

use FG\ASN1\Identifier;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Serializer\Curves\NamedCurveSerializer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FG\ASN1\Object;

class EcParamsCommand extends AbstractCommand
{

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('ecparams')
            ->setDescription('Dumps the elliptic curve parameters')
            ->addOption('curve', null, InputOption::VALUE_REQUIRED, 'Specify a named curve')
            ->addOption('outfile', null, InputOption::VALUE_OPTIONAL, 'Optionally direct output to a file')
            ->addOption('explicit', null, InputOption::VALUE_NONE, 'Dump explicit parameters instead of name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \FG\ASN1\Exception\ParserException
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curveName = $input->getOption('curve');

        if (! $curveName) {
            throw new \InvalidArgumentException('Curve name is required. Use "list-curves" to get available names.');
        }

        $curve = CurveFactory::getCurveByName($curveName);
        if ($input->getOption('explicit')) {
            echo 'explicit params file';
        } else {
            $serializer = new NamedCurveSerializer();
            $output->writeln($serializer->serialize($curve));
        }
    }

    /**
     * @param OutputInterface $output
     * @param Object $object
     * @param int $depth
     * @throws \FG\ASN1\Exception\NotImplementedException
     */
    private function printObject(OutputInterface $output, Object $object, $depth = 0)
    {
        $treeSymbol = '';
        $depthString = str_repeat('─', $depth);
        if ($depth > 0) {
            $treeSymbol = '├';
        }

        $name = Identifier::getShortName($object->getType());
        $output->write("{$treeSymbol}{$depthString}<comment>{$name}</comment> : ");
        $output->writeln($object->__toString());

        $content = $object->getContent();
        if (is_array($content)) {
            foreach ($object as $child) {
                $this->printObject($output, $child, $depth+1);
            }
        }
    }
}
