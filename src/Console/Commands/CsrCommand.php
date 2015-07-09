<?php

namespace Mdanter\Ecc\Console\Commands;


use Mdanter\Ecc\Crypto\Certificates\CsrSubjectFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\Certificates\CsrSerializer;
use Mdanter\Ecc\Serializer\Certificates\CsrSubjectSerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

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
            ->addOption('no-prompt', null, InputOption::VALUE_NONE, false)
            ->addOption('domain', null, InputOption::VALUE_REQUIRED, 'Elliptic curve domain - see `list-dsa`', 'secp256k1+sha256')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $domain = EccFactory::domain($input->getOption('domain'));

        $parser = $this->getPrivateKeySerializer($input, 'in');
        $loader = $this->getLoader($input, 'in');

        $data = $this->getPrivateKeyData($input, $loader, 'infile', 'data');
        $key = $parser->parse($data);

        $subject = new CsrSubjectFactory();
        $subject->commonName($input->getOption('CN'));

        // Only ask for further details + prompt for confirmation when this option isn't set
        if (!$input->getOption('no-prompt')) {
            foreach (self::$optMap as $char => $optionStr) {
                if ($input->getOption($char)) {
                    $option = $input->getOption($char);
                } else {
                    $q = new Question('Enter a ' . $optionStr . " [" . $char . "]: ");
                    $option = $helper->ask($input, $output, $q);
                    if (!$option) {
                        $output->writeln(" .. skipped " . $optionStr);
                        continue;
                    }
                }
                // $optionStr is function name + textual representation
                $subject->$optionStr($option);
            }

            $subjectInfo = $subject->getSubject();
            $values = $subjectInfo->getValues();

            $table = new Table($output);
            $table
                ->setHeaders(['Subject Info', 'Data'])
                ->setRows(array_map(
                    function ($key) use ($values) {
                        return [$key, $values[$key]];
                    },
                    array_keys($values)
                ));
            ;
            $table->render();

            $confirmationQ = new ConfirmationQuestion('Is the above information correct? [y/N]', false);
            $option = $helper->ask($input, $output, $confirmationQ);
            if (!$option) {
                $output->writeln('Will not create CSR');
                return;
            }
        } else {
            $subjectInfo = $subject->getSubject();
        }

        // Produce the CSR
        $csr = $domain->getCsr($subjectInfo, $key);

        $output->write((
            new CsrSerializer(
                new CsrSubjectSerializer(),
                new DerPublicKeySerializer(),
                new DerSignatureSerializer()
            ))->serialize($csr));

    }
}