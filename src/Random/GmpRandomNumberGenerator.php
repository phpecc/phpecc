<?php

namespace Mdanter\Ecc\Random;

class GmpRandomNumberGenerator implements RandomNumberGeneratorInterface
{
    /**
     * @param bool $noWarn
     */
    public function __construct($noWarn = false)
    {
        if ($noWarn !== true) {
            trigger_error('Using non-secure random number generator.', E_USER_WARNING);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\RandomNumberGeneratorInterface::generate()
     */
    public function generate($max)
    {
        $random = gmp_strval(gmp_random());
        $small_rand = rand();

        while (gmp_cmp($random, $max) > 0) {
            $random = gmp_div($random, $small_rand, GMP_ROUND_ZERO);
        }

        return gmp_strval($random);
    }
}
