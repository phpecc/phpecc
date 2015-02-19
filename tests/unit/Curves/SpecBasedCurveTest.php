<?php

namespace Mdanter\Ecc\Tests\Curves;

use Mdanter\Ecc\GeneratorPoint;
use Symfony\Component\Yaml\Yaml;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Signature\Signer;

class SpecBasedCurveTest extends \PHPUnit_Framework_TestCase
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

    public function getHmacTestSet()
    {
        $yaml = new Yaml();
        $files = $this->getFiles();
        $datasets = [];

        foreach ($files as $file) {
            $data = $yaml->parse($file);

            if (! isset($data['hmac'])) {
                continue;
            }

            $generator = CurveFactory::getGeneratorByName($data['name']);

            foreach ($data['hmac'] as $sig) {
                $datasets[] = [
                    $data['name'],
                    $generator,
                    isset($sig['size']) ? $sig['size'] : 0,
                    $sig['key'],
                    $sig['algo'],
                    $sig['message'],
                    strtolower($sig['k']),
                    strtolower($sig['r']),
                    strtolower($sig['s'])
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getHmacTestSet()
     */
    public function testHmacSignatures($name, GeneratorPoint $generator, $size, $privKey, $algo, $message, $eK, $eR, $eS)
    {
        $adapter = $generator->getAdapter();

        $key = $generator->getPrivateKeyFrom($adapter->hexDec($privKey));
        $hash = $adapter->hexDec(hash($algo, $message, false));

        $drbg = RandomGeneratorFactory::getHmacRandomGenerator($key, $hash, $algo);
        $signer = new Signer($adapter);

        if ($size > 0) {
            $hash = $adapter->baseConvert(substr($adapter->baseConvert($hash, 10, 2), 0, $size), 2, 10);
        }

        //$k = $drbg->generate($generator->getOrder());
        $signature = $signer->sign($key, $hash, $adapter->hexDec($eK));

        //$this->assertEquals($adapter->hexDec($eK), $k, 'k');
        $r = $adapter->decHex($signature->getR());
        $s = $adapter->decHex($signature->getS());
        $this->assertEquals($eR, $r, "r: $eR == $r");
        $this->assertEquals($eS, $s, "r: $eS == $s");
    }
}