<?php

namespace Mdanter\Ecc\Console\Commands;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParsePrivateKeyCommand extends Command
{

    protected function configure()
    {
        $this->setName('parse-privkey')->setDescription('Parse a PEM encoded private key (without its delimiters).')
            ->addArgument('data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = new PemPrivateKeySerializer();
        
        $data = $input->getArgument('data');
        $key = $parser->parse($data);
        
        $output->writeln('');
        
        $output->writeln('<comment>Private key information</comment>');
        $output->writeln('');
        $output->writeln('<info>Curve type</info> : ' . $key->getCurve()->getName());
        $output->writeln('<info>Secret</info>     : ' . $key->getSecret());
        
        $output->writeln('');
        
        $key = $key->getPublicKey();
        
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
