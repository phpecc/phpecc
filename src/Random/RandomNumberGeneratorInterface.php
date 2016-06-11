<?php

namespace Mdanter\Ecc\Random;

interface RandomNumberGeneratorInterface
{
    /**
     * Generate a random number between 0 and the specified upper boundary.
     *
     * @param resource|\GMP $max Upper boundary, inclusive
     */
    public function generate($max);
}
