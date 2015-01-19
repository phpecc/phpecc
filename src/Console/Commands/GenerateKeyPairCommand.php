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

class GenerateKeyPairCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('genkey')->setDescription('Generate a new keypair.')
            ->addOption('curve', 'c', InputOption::VALUE_REQUIRED, 
                        'Curve name. Use \'list-curves\' for available names.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curveName = $input->getOption('curve');
        
        if (! $curveName) {
            throw new \InvalidArgumentException('Curve name is required. Use "list-curves" to get available names.');
        }
        
        $generator = CurveFactory::getGeneratorByName($curveName);
        
        $privKeySerializer = new PemPrivateKeySerializer();
        $privKey = $generator->createPrivateKey();
        
        $output->writeln(array(
            '-----BEGIN EC PRIVATE KEY-----',
            $this->formatBase64($privKeySerializer->serialize($privKey)),
            '-----END EC PRIVATE KEY-----'
        ));
        
        $pubKeySerializer = new PemPublicKeySerializer();
        $pubKey = $privKey->getPublicKey();
        
        $output->writeln(array(
            '-----BEGIN PUBLIC KEY-----',
            $this->formatBase64($pubKeySerializer->serialize($pubKey)),
            '-----END PUBLIC KEY-----'
        ));
    }
    
    protected function formatBase64($string)
    {
        return trim(chunk_split($string, 64));
    }
}
