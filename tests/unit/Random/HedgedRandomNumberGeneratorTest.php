<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Random;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Key\PrivateKey;
use Mdanter\Ecc\Random\HedgedRandomNumberGenerator;
use Mdanter\Ecc\Random\HmacRandomNumberGenerator;
use Mdanter\Ecc\Tests\AbstractTestCase;

class HedgedRandomNumberGeneratorTest extends AbstractTestCase
{
    public function testRequireValidAlgorithm()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported hashing algorithm');

        $math = EccFactory::getAdapter();
        $g = EccFactory::getNistCurves()->generator192();
        $privateKey  = new PrivateKey($math, $g, gmp_init(1, 10));
        $hash = gmp_init(hash('sha256', 'message', false), 16);

        new HedgedRandomNumberGenerator($math, $privateKey, $hash, 'sha256aaaa');
    }

    public function testOutputDynamic()
    {;

        $math = EccFactory::getAdapter();
        $g = EccFactory::getNistCurves()->generator192();
        $privateKey  = new PrivateKey($math, $g, gmp_init(random_int(1, PHP_INT_MAX), 10));

        $hash = gmp_init(hash('sha256', 'message', false), 16);
        $rng = new HedgedRandomNumberGenerator($math, $privateKey, $hash, 'sha256');
        $x = $rng->generate($g->getOrder());
        $y = $rng->generate($g->getOrder());
        $this->assertNotSame(gmp_strval($x, 16), gmp_strval($y, 16));
    }
}
