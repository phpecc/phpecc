<?php

namespace Mdanter\Ecc\Console\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Mdanter\Ecc\Console\Commands\Helper\KeyTextDumper;

class ParsePrivateKeyCommand extends AbstractCommand
{

    /**
     *
     */
    protected function configure()
    {
        $this->setName('parse-privkey')->setDescription('Parse a PEM encoded private key (without its delimiters).')
            ->addArgument('data', InputArgument::OPTIONAL)
            ->addOption('infile', null, InputOption::VALUE_OPTIONAL)
            ->addOption(
                'in',
                null,
                InputOption::VALUE_OPTIONAL,
                'Input format (der or pem). Defaults to pem.',
                'pem'
            )
            ->addOption('rewrite', null, InputOption::VALUE_NONE, 'Regenerate and output the PEM data from the parsed key.', null);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
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

        if ($input->getOption('rewrite')) {
            $output->writeln($parser->serialize($key));
        }
    }
}
