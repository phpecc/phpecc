<?php

namespace Mdanter\Ecc\Console\Commands;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MessageSignCommand extends AbstractCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('sign')
            ->setDescription('Calculate a signature for a given file and private key')
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('infile', null, InputOption::VALUE_REQUIRED, 'Key file')
            ->addOption('in', null, InputOption::VALUE_OPTIONAL, 'Key file input format (der or pem). Defaults to pem.', 'pem')
            ->addOption('curve', null, InputOption::VALUE_OPTIONAL, 'Curve to sign over', 'secp256k1')
            ->addOption('algo', null, InputOption::VALUE_OPTIONAL, 'Hashing algorithm', 'sha256')
            ->addOption('det-sig', null, InputOption::VALUE_NONE, 'Use deterministic signatures')
        ;

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $math = EccFactory::getAdapter();

        // Check the hashing algorithm
        $hashSelection = $input->getOption('algo');
        $curveSelection = $input->getOption('curve');
        $domain = EccFactory::domain("$curveSelection+$hashSelection");

        // Parse the private key file
        $parser = $this->getPrivateKeySerializer($input, 'in');
        $loader = $this->getLoader($input, 'in');

        $keyData = $this->getPrivateKeyData($input, $loader, 'infile', null);
        $key = $parser->parse($keyData);

        // Check the target file exists
        $data = $this->getUserFile($input, 'file');

        $hasher = $domain->getHasher();
        $hashOut = $hasher($data);
        $hash = $math->hexDec($hashOut);

        $useDetSigs = $input->getOption('det-sig');

        if ($useDetSigs) {
            $random = RandomGeneratorFactory::getHmacRandomGenerator($key, $hash, $hashSelection);
        } else {
            $random = RandomGeneratorFactory::getUrandomGenerator();
        }

        $generator = $domain->getGenerator();
        $k = $random->generate($generator->getOrder());

        $signer = $domain->getSigner();
        $signature = $signer->sign($key, $hash, $k);

        $sigSerializer = new DerSignatureSerializer();
        $sig = $sigSerializer->serialize($signature);

        $binary = base64_encode($sig);
        $output->writeln($binary);
        return 0;
    }
}