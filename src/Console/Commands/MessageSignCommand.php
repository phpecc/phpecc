<?php

namespace Mdanter\Ecc\Console\Commands;

use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Mdanter\Ecc\Serializer\Util\HashAlgorithmOidMapper;
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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Check the hashing algorithm
        $hashAlgo = $input->getOption('algo');
        HashAlgorithmOidMapper::getHashAlgorithmOid($hashAlgo);

        // Parse the private key file
        $parser = $this->getPrivateKeySerializer($input, 'in');
        $loader = $this->getLoader($input, 'in');

        $keyData = $this->getPrivateKeyData($input, $loader, 'infile', null);
        $key = $parser->parse($keyData);

        // Check the target file exists
        $data = $this->getUserFile($input, 'file');

        $math = EccFactory::getAdapter();
        $curve = CurveFactory::getCurveByName($input->getOption('curve'));
        $generator = CurveFactory::getGeneratorByName($input->getOption('curve'));
        $hash = $math->hexDec(hash($hashAlgo, $data));
        $useDetSigs = $input->getOption('det-sig');

        if ($useDetSigs) {
            $random = RandomGeneratorFactory::getHmacRandomGenerator($key, $hash, $hashAlgo);
        } else {
            $random = RandomGeneratorFactory::getUrandomGenerator();
        }

        $signer = new Signer($math);
        $signature = $signer->sign($key, $hash, $random->generate($generator->getOrder()));

        $sigSerializer = new DerSignatureSerializer();
        $output->writeln($sigSerializer->serialize($signature));
    }
}