<?php

namespace Mdanter\Ecc\Console\Commands\Helper;

use Symfony\Component\Console\Output\OutputInterface;
use Mdanter\Ecc\Crypto\PublicKeyInterface;
use Mdanter\Ecc\Crypto\PrivateKeyInterface;

class KeyTextDumper
{
    public static function dumpPublicKey(OutputInterface $output, PublicKeyInterface $key)
    {
        $output->writeln('<comment>Public key information</comment>');
        $output->writeln('');
        $output->writeln('<info>Curve type</info> : '.$key->getCurve()->getName());
        $output->writeln('<info>X</info>          : '.$key->getPoint()->getX());
        $output->writeln('<info>Y</info>          : '.$key->getPoint()->getY());
        $output->writeln('<info>Order</info>      : '.(empty($key->getPoint()->getOrder()) ? '<null>' : $key->getPoint()->getOrder()));
    }

    public static function dumpPrivateKey(OutputInterface $output, PrivateKeyInterface $key)
    {
        $output->writeln('<comment>Private key information</comment>');
        $output->writeln('');
        $output->writeln('<info>Curve type</info> : '.$key->getCurve()->getName());
        $output->writeln('<info>Secret</info>     : '.$key->getSecret());
    }
}
