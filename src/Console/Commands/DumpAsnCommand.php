<?php

namespace Mdanter\Ecc\Console\Commands;

use FG\ASN1\Identifier;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FG\ASN1\Object;

class DumpAsnCommand extends AbstractCommand
{

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('dump-asn')
            ->setDescription('Dumps the ASN.1 object structure.')
            ->addArgument('data', InputArgument::OPTIONAL)
            ->addOption('infile', null, InputOption::VALUE_OPTIONAL)
            ->addOption('in', null, InputOption::VALUE_OPTIONAL, 'Input format (der or pem). Defaults to pem.', 'pem');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \FG\ASN1\Exception\ParserException
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loader = $this->getLoader($input, 'in');
        $data = $this->getPrivateKeyData($input, $loader, 'infile', 'data');

        $asnObject = Object::fromBinary(base64_decode($data));
        $this->printObject($output, $asnObject);
    }

    /**
     * @param OutputInterface $output
     * @param Object $object
     * @param int $depth
     * @return void
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
