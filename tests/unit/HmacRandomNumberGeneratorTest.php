<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\PrivateKey;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Signature\Signer;

class HmacRandomNumberGeneratorTest extends AbstractTestCase
{
    /**
     * @var \Mdanter\Ecc\MathAdapterInterface
     */
    protected $math;
    /**
     * @var \Mdanter\Ecc\GeneratorPoint
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
        $drbg = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $hash, 'sha256a');
    }

    public function testDeterministicSign()
    {

        $f = file_get_contents(__DIR__.'/../data/rfc6979.json');
        $json = json_decode($f);

        foreach ($json->test as $c => $test) {

            // Initialize private key and message hash (decimal)
            $privateKey  = new PrivateKey($this->math, $this->G, $this->math->hexDec($test->privKey));
            $messageHash = $this->math->hexDec(hash('sha256', $test->message));

            // Derive K
            $drbg = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $messageHash, 'sha256');
            $k    = $drbg->generate($this->G->getOrder());

            $signer = new Signer($this->math);
            $sig    = $signer->sign($privateKey, $messageHash, $k);

            // K must be correct (from privatekey and message hash)
            $this->assertEquals(strtolower($test->expectedK), $this->math->decHex($k));

            // R and S should be correct
            $rHex = $this->math->dechex($sig->getR());
            $sHex = $this->math->decHex($sig->getS());
            $this->assertSame($test->expectedRS, $rHex.$sHex);
        }
    }

}