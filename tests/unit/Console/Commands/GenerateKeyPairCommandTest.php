<?php

namespace Mdanter\Ecc\Tests\Console\Commands;

use Mdanter\Ecc\Console\Commands\GenerateKeyPairCommand;
use Mdanter\Ecc\Math\MathAdapterFactory;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Random\RandomGeneratorFactory;

class GenerateKeyPairCommandTest extends AbstractCommandTest
{

    /**
     * @dataProvider getAdapters
     */
    public function testGenerateKeyPairWithPredefinedSecret(MathAdapterInterface $adapter)
    {
        $commandTester = $this->getCommandTester(new GenerateKeyPairCommand(), 'genkey');
        $expected = file_get_contents(__DIR__.'/../../../data/generated-keypair.pem');

        $secret = '105886814118965842118146815191867355142743831281343651404754056074495577342758';
        $randomGenerator = $this->getMock($this->classRngInterface);

        $randomGenerator->expects($this->once())
            ->method('generate')
            ->willReturn($secret);

        MathAdapterFactory::forceAdapter($adapter);
        RandomGeneratorFactory::forceGenerator($randomGenerator);

        $commandTester->execute(array('--curve' => 'nist-p256'));

        $this->assertEquals($this->normalize($expected), $this->normalize($commandTester->getDisplay()));

        MathAdapterFactory::forceAdapter(null);
        RandomGeneratorFactory::forceGenerator(null);
    }
}
