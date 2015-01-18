<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\Math\BcMathUtils;
use Mdanter\Ecc\RandomNumberGeneratorInterface;

class BcMathRandomNumberGenerator implements RandomNumberGeneratorInterface
{
    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\RandomNumberGeneratorInterface::generate()
     */
    public function generate($max)
    {
        return (string) BcMathUtils::bcrand($max);
    }    
}
