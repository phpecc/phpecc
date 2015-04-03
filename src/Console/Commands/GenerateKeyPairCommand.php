<?php

namespace Mdanter\Ecc\Console\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Mdanter\Ecc\Curves\CurveFactory;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

class GenerateKeyPairCommand extends AbstractCommand
{

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('genkey')->setDescription('Generate a new keypair.')
            ->addOption(
                'curve',
                'c',
                InputOption::VALUE_REQUIRED,
                'Curve name. Use \'list-curves\' for available names.'
            )
            ->addOption(
                'out',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Output format (der or pem). Defaults to pem.',
                'pem'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curveName = $input->getOption('curve');

        if (! $curveName) {
            throw new \InvalidArgumentException('Curve name is required. Use "list-curves" to get available names.');
        }

        $generator = CurveFactory::getGeneratorByName($curveName);

        if ($output instanceof ConsoleOutputInterface) {
            $output->getErrorOutput()->writeln('Using curve "'.$curveName."'");
        }

        $privKeySerializer = $this->getPrivateKeySerializer($input, 'out');
        $pubKeySerializer = $this->getPublicKeySerializer($input, 'out');

        $privKey = $generator->createPrivateKey();
        $output->writeln($privKeySerializer->serialize($privKey));

        $pubKey = $privKey->getPublicKey();
        $output->writeln($pubKeySerializer->serialize($pubKey));
    }
}
