<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\RandomNumberGeneratorInterface;

class BcMathRandomNumberGenerator implements RandomNumberGeneratorInterface
{
    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\RandomNumberGeneratorInterface::generate()
     */
    public function generate($max)
    {
        return BcMathUtils::bcrand($n);
    }    
}