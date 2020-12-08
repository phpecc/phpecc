<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests\Math;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Tests\AbstractTestCase;

abstract class NumberTheoryTestPhpunit6 extends AbstractTestCase
{
    protected function setUp()
    {
        // todo: in the future, turn these into data providers instead
        // file containing a json array of {compressed=>'', decompressed=>''} values
        // of compressed and uncompressed ECDSA public keys (testing secp256k1 curve)
        $file_comp = TEST_DATA_DIR.'/compression.json';

        if (! file_exists($file_comp)) {
            $this->fail('Key compression input data not found');
        }

        $file_sqrt = TEST_DATA_DIR.'/square_root_mod_p.json';
        if (! file_exists($file_sqrt)) {
            $this->fail('Square root input data not found');
        }
        $this->generator = EccFactory::getSecgCurves()->generator256k1();
        $this->compression_data = json_decode(file_get_contents($file_comp));

        $this->sqrt_data = json_decode(file_get_contents($file_sqrt));
    }
}
