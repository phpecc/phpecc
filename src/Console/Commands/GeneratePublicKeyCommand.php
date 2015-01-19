<?php

namespace Mdanter\Ecc\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\KeyFormat\PKCS8Format;
use Symfony\Component\Console\Input\InputOption;
use Mdanter\Ecc\KeyFormat\X509PublicKeyFormatter;
use Mdanter\Ecc\KeyFormat\PemPrivateKeyFormatter;
use Mdanter\Ecc\KeyFormat\PemPublicKeyFormatter;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Math\MathAdapterFactory;
use Symfony\Component\Console\Input\InputArgument;
use Mdanter\Ecc\File\PemLoader;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;

class GeneratePublicKeyCommand extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('encode-pubkey')->setDescription('Encodes the public key from a PEM encoded private key to PEM format.')
            ->addArgument('data', InputArgument::OPTIONAL)
            ->addOption('infile', null, InputOption::VALUE_OPTIONAL)
            ->addOption('in', null, InputOption::VALUE_OPTIONAL,
                'Input format (der or pem). Defaults to pem.', 'pem')
            ->addOption('out', null, InputOption::VALUE_OPTIONAL,
                'Output format (der or pem). Defaults to pem.', 'pem');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pubKeySerializer = $this->getPublicKeySerializer($input, 'out');
        $privKeySerializer = $this->getPrivateKeySerializer($input, 'in');
        $loader = $this->getLoader($input, 'in');
        
        $data = $this->getPrivateKeyData($input, $loader, 'infile', 'data');
        $key = $privKeySerializer->parse($data);
        
        $output->writeln($pubKeySerializer->serialize($key->getPublicKey()));
    }
    
    protected function formatBase64($string)
    {
        return trim(chunk_split($string, 64, PHP_EOL));
    }
}
