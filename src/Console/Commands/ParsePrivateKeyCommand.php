<?php

namespace Mdanter\Ecc\Console\Commands;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Mdanter\Ecc\File\PemLoader;
use Mdanter\Ecc\Console\Commands\Helper\KeyTextDumper;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;

class ParsePrivateKeyCommand extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('parse-privkey')->setDescription('Parse a PEM encoded private key (without its delimiters).')
            ->addArgument('data', InputArgument::OPTIONAL)
            ->addOption('infile', null, InputOption::VALUE_OPTIONAL)
            ->addOption('in', null, InputOption::VALUE_OPTIONAL,
                'Input format (der or pem). Defaults to pem.', 'pem');
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = $this->getPrivateKeySerializer($input, 'in');
        $loader = $this->getLoader($input, 'in');
        
        $data = $this->getPrivateKeyData($input, $loader, 'infile', 'data');
        $key = $parser->parse($data);
        
        $output->writeln('');
        KeyTextDumper::dumpPrivateKey($output, $key);
        $output->writeln('');
        
        $output->writeln('');
        KeyTextDumper::dumpPublicKey($output, $key->getPublicKey());
        $output->writeln('');
    }
}
