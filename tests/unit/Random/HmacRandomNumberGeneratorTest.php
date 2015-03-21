<?php

namespace Mdanter\Ecc\Tests\Random;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Key\PrivateKey;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Crypto\Routines\Signature\Signer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class HmacRandomNumberGeneratorTest extends AbstractTestCase
{
    /**
     * @var \Mdanter\Ecc\Math\MathAdapterInterface
     */
    protected $math;
    /**
     * @var \Mdanter\Ecc\Primitives\GeneratorPoint
     */
    protected $G;

    public function setUp()
    {
        $this->math = EccFactory::getAdapter();
        $this->G = EccFactory::getSecgCurves()->generator256k1();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage HMACDRGB: Hashing algorithm not found
     */
    public function testRequireValidAlgorithm()
    {
        $privateKey  = new PrivateKey($this->math, $this->G, 1);
        $hash = hash('sha256', 'message');

        RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $hash, 'sha256a');
    }

    public function testDeterministicSign()
    {
        $f = file_get_contents(__DIR__.'/../../data/rfc6979.json');
        $json = json_decode($f);
        $math = $this->math;
        $G = $this->G;

        foreach ($json->test as $test) {

            // Initialize private key and message hash (decimal)
            $privateKey  = new PrivateKey($math, $G, $math->hexDec($test->privKey));
            $messageHash = $this->math->hexDec(hash('sha256', $test->message));

            // Derive K
            $drbg = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $messageHash, 'sha256');

            // K must be correct (from privatekey and message hash)
            $k    = $drbg->generate($G->getOrder());
            $this->assertEquals(strtolower($test->expectedK), $math->decHex($k));

            $signer = new Signer($math);
            $sig    = $signer->sign($privateKey, $messageHash, $k);

            // R and S should be correct
            $rHex = $math->dechex($sig->getR());
            $sHex = $math->decHex($sig->getS());
            $this->assertSame($test->expectedRS, $rHex.$sHex);
        }
    }
}
