<?php

namespace Mdanter\Ecc\Tests\Console\Commands;

use Mdanter\Ecc\Console\Commands\GeneratePublicKeyCommand;

class GeneratePublicKeyCommandTest extends AbstractCommandTest
{
    public function testOutputIsCompatibleWithOpenSSLOutput()
    {
        $commandTester = $this->getCommandTester(new GeneratePublicKeyCommand(), 'encode-pubkey');
        
        $data = file_get_contents(__DIR__ . '/../../../data/openssl-priv.key');
        $expected = file_get_contents(__DIR__ . '/../../../data/openssl-pub.key');
        
        $commandTester->execute(array('data' => $data));
        
        $this->assertEquals($this->normalize($expected), $this->normalize($commandTester->getDisplay()));
    }
}