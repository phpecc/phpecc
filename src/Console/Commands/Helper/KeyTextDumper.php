<?php

namespace Mdanter\Ecc\Console\Commands\Helper;

use Symfony\Component\Console\Output\OutputInterface;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;

class KeyTextDumper
{
    /**
     * @param OutputInterface $output
     * @param PublicKeyInterface $key
     */
    public static function dumpPublicKey(OutputInterface $output, PublicKeyInterface $key)
    {
        $order = $key->getPoint()->getOrder();
        $output->writeln('<comment>Public key information</comment>');
        $output->writeln('');
        $output->writeln('<info>Curve type</info> : '.$key->getCurve()->getName());
        $output->writeln('<info>X</info>          : '.$key->getPoint()->getX());
        $output->writeln('<info>Y</info>          : '.$key->getPoint()->getY());
        $output->writeln('<info>Order</info>      : '.(empty($order) ? '<null>' : $key->getPoint()->getOrder()));
    }

    /**
     * @param OutputInterface $output
     * @param PrivateKeyInterface $key
     */
    public static function dumpPrivateKey(OutputInterface $output, PrivateKeyInterface $key)
    {
        $output->writeln('<comment>Private key information</comment>');
        $output->writeln('');
        $output->writeln('<info>Curve type</info> : '.$key->getCurve()->getName());
        $output->writeln('<info>Secret</info>     : '.$key->getSecret());
    }
}
