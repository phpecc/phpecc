<?php

namespace Mdanter\Ecc\Console\Commands;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Curves\CurveFactory;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;

class GenerateKeyPairCommand extends AbstractCommand
{

    protected function configure()
    {
        $this
            ->setName('genkey')->setDescription('Generate a new keypair.')
            ->addOption('curve', 'c', InputOption::VALUE_REQUIRED, 
                        'Curve name. Use \'list-curves\' for available names.')
            ->addOption('out', 'p', InputOption::VALUE_OPTIONAL,
                        'Output format (der or pem). Defaults to pem.', 'pem');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curveName = $input->getOption('curve');
        
        if (! $curveName) {
            throw new \InvalidArgumentException('Curve name is required. Use "list-curves" to get available names.');
        }
        
        $generator = CurveFactory::getGeneratorByName($curveName);
        
        if ($output instanceof ConsoleOutputInterface) {
            $output->getErrorOutput()->writeln('Using curve "' . $curveName . "'" );
        }
        
        $privKeySerializer = $this->getPrivateKeySerializer($input, 'out');
        $pubKeySerializer = $this->getPublicKeySerializer($input, 'out');
        
        $privKey = $generator->createPrivateKey();
        $output->writeln($privKeySerializer->serialize($privKey));
        
        $pubKey = $privKey->getPublicKey();
        $output->writeln($pubKeySerializer->serialize($pubKey));
    }
}
