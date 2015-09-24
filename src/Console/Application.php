<?php

namespace Mdanter\Ecc\Console;

use Mdanter\Ecc\Console\Commands\CsrCommand;
use Mdanter\Ecc\Console\Commands\DsaInfoCommand;
use Symfony\Component\Console\Application as ConsoleApplication;
use Mdanter\Ecc\Console\Commands\GenerateKeyPairCommand;
use Mdanter\Ecc\Console\Commands\HexDecCommand;
use Mdanter\Ecc\Console\Commands\DecHexCommand;
use Mdanter\Ecc\Console\Commands\ParsePublicKeyCommand;
use Mdanter\Ecc\Console\Commands\ParsePrivateKeyCommand;
use Mdanter\Ecc\Console\Commands\GeneratePublicKeyCommand;
use Mdanter\Ecc\Console\Commands\ListCurvesCommand;
use Mdanter\Ecc\Console\Commands\EcParamsCommand;
use Mdanter\Ecc\Console\Commands\DumpAsnCommand;
use Mdanter\Ecc\Console\Commands\MessageSignCommand;

class Application extends ConsoleApplication
{

    /**
     * @return array|\Symfony\Component\Console\Command\Command[]
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new DumpAsnCommand();
        $commands[] = new GenerateKeyPairCommand();
        $commands[] = new GeneratePublicKeyCommand();
        $commands[] = new ListCurvesCommand();
        $commands[] = new DsaInfoCommand();
        $commands[] = new EcParamsCommand();
        $commands[] = new ParsePrivateKeyCommand();
        $commands[] = new ParsePublicKeyCommand();
        $commands[] = new HexDecCommand();
        $commands[] = new DecHexCommand();
        $commands[] = new CsrCommand();
        $commands[] = new MessageSignCommand();

        return $commands;
    }
}
