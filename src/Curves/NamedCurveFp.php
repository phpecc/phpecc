<?php

namespace Mdanter\Ecc\Curves;

use Mdanter\Ecc\Primitives\CurveFp;
use Mdanter\Ecc\Math\MathAdapterInterface;

class NamedCurveFp extends CurveFp
{
    /**
     * @var int|string
     */
    private $name;

    /**
     * @param int|string           $name
     * @param int|string           $prime
     * @param int|string           $a
     * @param MathAdapterInterface $b
     * @param MathAdapterInterface $adapter
     */
    public function __construct($name, $prime, $a, $b, MathAdapterInterface $adapter)
    {
        $this->name = $name;

        parent::__construct($prime, $a, $b, $adapter);
    }

    /**
     * @return int|string
     */
    public function getName()
    {
        return $this->name;
    }
}
