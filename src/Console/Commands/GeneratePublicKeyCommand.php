<?php

namespace Mdanter\Ecc\Console\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GeneratePublicKeyCommand extends AbstractCommand
{

    /**
     *
     */
    protected function configure()
    {
        $this->setName('encode-pubkey')->setDescription('Encodes the public key from a PEM encoded private key to PEM format.')
            ->addArgument('data', InputArgument::OPTIONAL)
            ->addOption('infile', null, InputOption::VALUE_OPTIONAL)
            ->addOption(
                'in',
                null,
                InputOption::VALUE_OPTIONAL,
                'Input format (der or pem). Defaults to pem.',
                'pem'
            )
            ->addOption(
                'out',
                null,
                InputOption::VALUE_OPTIONAL,
                'Output format (der or pem). Defaults to pem.',
                'pem'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pubKeySerializer = $this->getPublicKeySerializer($input, 'out');
        $privKeySerializer = $this->getPrivateKeySerializer($input, 'in');
        $loader = $this->getLoader($input, 'in');

        $data = $this->getPrivateKeyData($input, $loader, 'infile', 'data');
        $key = $privKeySerializer->parse($data);

        $output->writeln($pubKeySerializer->serialize($key->getPublicKey()));
    }

    /**
     * @param $string
     * @return string
     */
    protected function formatBase64($string)
    {
        return trim(chunk_split($string, 64, PHP_EOL));
    }
}
