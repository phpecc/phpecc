<?php

namespace Mdanter\Ecc\Tests\Primitives;


use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Primitives\EcDomain;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Tests\AbstractTestCase;
use Mdanter\Ecc\Util\Hasher;

class EcDomainTest extends AbstractTestCase
{
    public function testEcDomain()
    {
        $algo = 'sha256';
        $name = 'secp256k1';
        $math = new Gmp();
        $curve = CurveFactory::getCurveByName($name);
        $generator = CurveFactory::getGeneratorByName($name);
        $hasher = new Hasher($math, $algo);
        $domain = new EcDomain($math, $curve, $generator, $hasher);

        $this->assertEquals($hasher, $domain->getHasher());
        $this->assertEquals($generator, $domain->getGenerator());
        $this->assertEquals($curve, $domain->getCurve());
        $this->assertEquals('ecdsa+' . $algo, $domain->getSigAlgorithm());
        $this->assertInstanceOf(Signer::class, $domain->getSigner());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEcDomainWithBadCombination()
    {
        $algo = 'sha256';
        $math = new Gmp();
        $curve = CurveFactory::getCurveByName('secp256k1');
        $generator = CurveFactory::getGeneratorByName('nist-p521');
        $hasher = new Hasher($math, $algo);

        new EcDomain($math, $curve, $generator, $hasher);
    }

    public function testFactoryMethod()
    {
        $curveName = 'secp256k1';
        $hashAlgorithm = 'sha256';
        $domain = EccFactory::getDomain($curveName, $hashAlgorithm);
        $this->assertInstanceOf(EcDomain::class, $domain);

        $this->assertEquals($curveName, $domain->getCurve()->getName());
    }
}