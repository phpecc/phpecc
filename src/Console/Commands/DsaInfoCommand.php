<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 07/07/15
 * Time: 23:54
 */

namespace Mdanter\Ecc\Console\Commands;


use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Serializer\Util\HashAlgorithmOidMapper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DsaInfoCommand extends AbstractCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('list-dsa')
            ->setDescription('Lists curves and hashing algorithms supported by this tool');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $curves = CurveOidMapper::getNames();
        $hashAlgos = HashAlgorithmOidMapper::getNames();

        $output->writeln('');
        $output->writeln(" When using the sign command, you can specify any combination of the following curves and hashing algorithms");
        $output->writeln("   eg: secp256k1+sha256 ");
        $output->writeln("");
        for ($i = 0, $end = max(count($curves), count($hashAlgos)); $i < $end; $i++) {
            if ($i == 0) {
                $output->writeln(" <info>Supported curves:</info>    <info>Supported hashing algorithms:</info>");
                $output->writeln("   " . $curves[0] . "\t        " . $hashAlgos[0] . "");
                continue;
            }

            $c = isset($curves[$i]) ? $curves[$i] : '       ';
            $h = isset($hashAlgos[$i]) ? $hashAlgos[$i] : '       ';

            $output->writeln("   " . $c . "\t        " . $h . "");
        }

        $output->writeln("");
    }
}