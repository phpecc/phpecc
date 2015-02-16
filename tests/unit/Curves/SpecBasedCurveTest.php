<?php

namespace Mdanter\Ecc\Tests\Curves;

use Mdanter\Ecc\GeneratorPoint;
use Symfony\Component\Yaml\Yaml;
use Mdanter\Ecc\Curves\CurveFactory;

class SpecBasedCurveTest extends \PHPUnit_Framework_TestCase
{

    public function getTestSet()
    {
        $yaml = new Yaml();
        $files = [
            __DIR__ . '/../../specs/nist-p192.yml',
            __DIR__ . '/../../specs/nist-p224.yml',
            __DIR__ . '/../../specs/nist-p256.yml',
            __DIR__ . '/../../specs/nist-p384.yml',
            __DIR__ . '/../../specs/nist-p521.yml'
        ];
        $datasets = [];

        foreach ($files as $file) {
            $data = $yaml->parse($file);
            $generator = CurveFactory::getGeneratorByName($data['name']);

            foreach ($data['values'] as $testKeyPair) {
                $datasets[] = [
                    $data['name'],
                    $generator,
                    $testKeyPair['k'],
                    $testKeyPair['x'],
                    $testKeyPair['y']
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getTestSet()
     * @param GeneratorPoint $generator
     * @param string $k
     * @param string $expectedX
     * @param string $expectedY
     */
    public function testGetPublicKey($name, GeneratorPoint $generator, $k, $expectedX, $expectedY)
    {
        $adapter = $generator->getAdapter();

        $privateKey = $generator->getPrivateKeyFrom($k);
        $publicKey = $privateKey->getPublicKey();

        $this->assertEquals($adapter->hexDec($expectedX), $publicKey->getPoint()->getX(), $name);
        $this->assertEquals($adapter->hexDec($expectedY), $publicKey->getPoint()->getY(), $name);
    }

}