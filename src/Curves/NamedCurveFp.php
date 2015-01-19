<?php

namespace Mdanter\Ecc\Curves;

use Mdanter\Ecc\CurveFp;
use Mdanter\Ecc\MathAdapterInterface;

class NamedCurveFp extends CurveFp
{
    
    private $name;
    
    public function __construct($name, $prime, $a, $b, MathAdapterInterface $adapter)
    {
        $this->name = $name;
        
        parent::__construct($prime, $a, $b, $adapter);
    }    
    
    public function getName()
    {
        return $this->name;
    }
}
