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
use Mdanter\Ecc\File\PemLoader;
use Symfony\Component\Console\Input\InputArgument;

class ParsePublicKeyCommand extends Command
{

    protected function configure()
    {
        $this->setName('parse-pubkey')->setDescription('Parse a PEM encoded public key, without its delimiters.')
            ->addArgument('data', InputArgument::OPTIONAL)
            ->addOption('infile', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = new PemPublicKeySerializer();
        
        if ($infile = $input->getOption('infile')) {
            $loader = new PemLoader();
        
            if (! file_exists($infile)) {
                $infile = getcwd() . '/' . $infile;
            }
        
            $data = $loader->loadPublicKeyData(realpath($infile));
        }
        else {
            $data = $input->getArgument('data');
        }
        
        $key = $parser->parse($data);
        
        $output->writeln('');
        
        $output->writeln('<comment>Public key information</comment>');
        $output->writeln('');
        $output->writeln('<info>Curve type</info> : ' . $key->getCurve()->getName());
        $output->writeln('<info>X</info>          : ' . $key->getPoint()->getX());
        $output->writeln('<info>Y</info>          : ' . $key->getPoint()->getY());
        $output->writeln('<info>Order</info>      : ' . (empty($key->getPoint()->getOrder()) ? '<null>' : $key->getPoint()->getOrder()));
        
        $output->writeln('');
    }
}
