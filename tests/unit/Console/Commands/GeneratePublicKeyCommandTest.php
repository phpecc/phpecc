<?php

namespace Mdanter\Ecc\Tests\Console\Commands;

use Mdanter\Ecc\Console\Commands\GeneratePublicKeyCommand;
use Mdanter\Ecc\Math\MathAdapterFactory;
use Mdanter\Ecc\Math\MathAdapterInterface;

class GeneratePublicKeyCommandTest extends AbstractCommandTest
{
    /**
     * @dataProvider getAdapters
     */
    public function testOutputIsCompatibleWithOpenSSLOutput(MathAdapterInterface $adapter)
    {
        MathAdapterFactory::forceAdapter($adapter);

        $commandTester = $this->getCommandTester(new GeneratePublicKeyCommand(), 'encode-pubkey');

        $data = file_get_contents(__DIR__.'/../../../data/openssl-priv.key');
        $expected = file_get_contents(__DIR__.'/../../../data/openssl-pub.key');

        $commandTester->execute(array('data' => $data));

        $this->assertEquals($this->normalize($expected), $this->normalize($commandTester->getDisplay()));

        MathAdapterFactory::forceAdapter(null);
    }
}
