<?php

namespace Mdanter\Ecc\Tests\Curves;

use Mdanter\Ecc\Tests\AbstractTestCase;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Symfony\Component\Yaml\Yaml;
use Mdanter\Ecc\Curves\CurveFactory;

class SpecBasedCurveTest extends AbstractTestCase
{

    public function getFiles()
    {
        return [
            __DIR__ . '/../../specs/secg-256r1.yml',
            __DIR__ . '/../../specs/secg-256k1.yml',
            __DIR__ . '/../../specs/secg-384r1.yml',
            __DIR__ . '/../../specs/nist-p192.yml',
            __DIR__ . '/../../specs/nist-p224.yml',
            __DIR__ . '/../../specs/nist-p256.yml',
            __DIR__ . '/../../specs/nist-p384.yml',
            __DIR__ . '/../../specs/nist-p521.yml'
        ];
    }

    public function getKeypairsTestSet()
    {
        $yaml = new Yaml();
        $files = $this->getFiles();
        $datasets = [];

        foreach ($files as $file) {
            $data = $yaml->parse($file);
            $generator = CurveFactory::getGeneratorByName($data['name']);

            foreach ($data['keypairs'] as $testKeyPair) {
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
     * @dataProvider getKeypairsTestSet()
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

    public function getDiffieHellmanTestSet()
    {
        $yaml = new Yaml();
        $files = $this->getFiles();
        $datasets = [];

        foreach ($files as $file) {
            $data = $yaml->parse($file);
            $generator = CurveFactory::getGeneratorByName($data['name']);

            foreach ($data['diffie'] as $testKeyPair) {
                $datasets[] = [
                    $data['name'],
                    $generator,
                    $testKeyPair['alice'],
                    $testKeyPair['bob'],
                    $testKeyPair['shared']
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getDiffieHellmanTestSet()
     * @param GeneratorPoint $generator
     * @param string $alice
     * @param string $bob
     * @param string $expectedX
     */
    public function testGetDiffieHellmanSharedSecret($name, GeneratorPoint $generator, $alice, $bob, $expectedX)
    {
        $adapter = $generator->getAdapter();

        $alicePrivKey = $generator->getPrivateKeyFrom($alice);
        $bobPrivKey = $generator->getPrivateKeyFrom($bob);

        $aliceDh = $alicePrivKey->createExchange($bobPrivKey->getPublicKey());
        $bobDh = $bobPrivKey->createExchange($alicePrivKey->getPublicKey());

        $this->assertEquals($aliceDh->calculateSharedKey(), $adapter->hexDec($expectedX));
        $this->assertEquals($bobDh->calculateSharedKey(), $adapter->hexDec($expectedX));
    }

}