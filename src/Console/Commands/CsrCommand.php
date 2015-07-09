<?php

namespace Mdanter\Ecc\Console\Commands;


use Mdanter\Ecc\Crypto\Certificates\CsrSubjectFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\Certificates\CsrSerializer;
use Mdanter\Ecc\Serializer\Certificates\CsrSubjectSerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CsrCommand extends AbstractCommand
{
    /**
     * @var array
     */
    private static $optMap = [
        'OU' => 'organizationUnit',
        'O' => 'organization',
        'ST' => 'state',
        'L' => 'locality',
        'C' => 'country'
    ];

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('csr')
            ->setDescription('Generates a certificate signing request (CSR) for the provided key')
            ->addArgument('data', InputArgument::OPTIONAL, 'Private key file')
            ->addOption('infile', null, InputOption::VALUE_OPTIONAL, 'Private key file option')
            ->addOption('in', null, InputOption::VALUE_OPTIONAL, 'Private key format (defaults to pem)', 'pem')
            ->addOption('CN', null, InputOption::VALUE_REQUIRED, 'Common Name (required)')
            ->addOption('O', null, InputOption::VALUE_REQUIRED, 'Organization')
            ->addOption('OU', null, InputOption::VALUE_REQUIRED, 'Organization Unit')
            ->addOption('ST', null, InputOption::VALUE_REQUIRED, 'State')
            ->addOption('L', null, InputOption::VALUE_REQUIRED, 'Locality')
            ->addOption('C', null, InputOption::VALUE_REQUIRED, 'Country')
            ->addOption('domain', null, InputOption::VALUE_REQUIRED, 'Elliptic curve domain - see `list-dsa`', 'secp256k1+sha256')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain = EccFactory::domain($input->getOption('domain'));

        $parser = $this->getPrivateKeySerializer($input, 'in');
        $loader = $this->getLoader($input, 'in');

        $data = $this->getPrivateKeyData($input, $loader, 'infile', 'data');
        $key = $parser->parse($data);

        $subject = new CsrSubjectFactory();
        $subject->commonName($input->getOption('CN'));
        foreach (array_keys(self::$optMap) as $char) {
            if ($input->getOption($char)) {
                // get function name
                $opt = self::$optMap[$char];
                $subject->$opt($input->getOption($char));
            }
        }

        $csr = $domain->getCsr($subject->getSubject(), $key);

        $output->write((
            new CsrSerializer(
                new CsrSubjectSerializer(),
                new DerPublicKeySerializer(),
                new DerSignatureSerializer()
            ))->serialize($csr));

    }
}