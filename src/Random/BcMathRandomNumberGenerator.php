<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\Math\BcMathUtils;
use Mdanter\Ecc\RandomNumberGeneratorInterface;

class BcMathRandomNumberGenerator implements RandomNumberGeneratorInterface
{
    public function __construct($noWarn = false)
    {
        if ($noWarn !== true) {
            trigger_error('Using non-secure random number generator.', E_USER_WARNING);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\RandomNumberGeneratorInterface::generate()
     */
    public function generate($max)
    {
        return (string)BcMathUtils::bcrand($max);
    }    
}
