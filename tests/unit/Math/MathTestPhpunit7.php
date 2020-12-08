<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Math;

use Mdanter\Ecc\Tests\AbstractTestCase;

abstract class MathTestPhpunit7 extends AbstractTestCase
{
    protected $knownPrimes;

    protected function setUp(): void
    {
        $file = TEST_DATA_DIR.'/primes.lst';

        if (! file_exists($file)) {
            $this->fail('Primes not found');
        }

        $lines = file($file);
        if (! $lines) {
            $this->fail('Empty prime file');
        }

        $this->knownPrimes = array_map(function ($i) {
            return intval($i);
        }, $lines);
    }
}
