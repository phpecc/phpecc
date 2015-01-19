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

class GeneratePublicKeyCommand extends Command
{

    protected function configure()
    {
        $this->setName('encode-pubkey')->setDescription('Encodes the public key from a PEM encoded private key to PEM format.')
            ->addArgument('data', InputArgument::OPTIONAL)
            ->addOption('infile', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pubKeySerializer = new PemPublicKeySerializer();
        $privKeySerializer = new PemPrivateKeySerializer(MathAdapterFactory::getAdapter(), $pubKeySerializer);
        
        if ($infile = $input->getOption('infile')) {
            $loader = new PemLoader();
            
            if (! file_exists($infile)) {
                $infile = getcwd() . '/' . $infile;
            }
            
            $data = $loader->loadPrivateKeyData(realpath($infile));
        }
        else {
            $data = $input->getArgument('data');
        }
        
        $key = $privKeySerializer->parse($data);
        
        $output->writeln(array(
            '-----BEGIN PUBLIC KEY-----',
            $this->formatBase64($pubKeySerializer->serialize($key->getPublicKey())),
            '-----END PUBLIC KEY-----'
        ));
    }
    
    protected function formatBase64($string)
    {
        return trim(chunk_split($string, 64, PHP_EOL));
    }
}
