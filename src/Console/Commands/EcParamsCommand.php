<?php

namespace Mdanter\Ecc\Console\Commands;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Serializer\Curves\EcParamsOidSerializer;
use Mdanter\Ecc\Serializer\Curves\EcParamsSerializer;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            $generator = CurveFactory::getGeneratorByName($curveName);
            $serializer = new EcParamsSerializer(new UncompressedPointSerializer($generator->getAdapter()));
            $data = $serializer->serialize($curve, $generator);
        } else {
            $serializer = new EcParamsOidSerializer();
            $data = $serializer->serialize($curve);
        }

        $output->writeln($data);
    }
}
