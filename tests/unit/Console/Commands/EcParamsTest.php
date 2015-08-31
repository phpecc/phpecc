<?php

namespace Mdanter\Ecc\Tests\Console\Commands;


use Mdanter\Ecc\Console\Commands\EcParamsCommand;

class EcParamsTest extends AbstractCommandTest
{
    public function testDumpEcParamsOid()
    {
        $expected = file_get_contents(__DIR__.'/../../../data/params.secp256k1.oid.pem');

        $commandTester = $this->getCommandTester(new EcParamsCommand(), 'ecparams');
        $commandTester->execute(array('--curve' => 'secp256k1'));

        $this->assertEquals($this->normalize($expected), $this->normalize($commandTester->getDisplay()));
    }

    public function testDumpEcParamsExplicit()
    {
        $expected = file_get_contents(__DIR__.'/../../../data/params.secp256k1.explicit.pem');

        $commandTester = $this->getCommandTester(new EcParamsCommand(), 'ecparams');
        $commandTester->execute(array('--curve' => 'secp256k1', '--explicit' => true));

        $this->assertEquals($this->normalize($expected), $this->normalize($commandTester->getDisplay()));
    }
}