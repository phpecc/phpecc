<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\Primitives\PointInterface;

interface EcMathInterface
{
    /**
     * Add $addend to the current value stored in the instance.
     *
     * @param  $addend
     * @return $this
     */
    public function add($addend);

    /**
     * Multiply $multiplicand by the current value stored in the instance.
     *
     * @param  $multiplicand
     * @return $this
     */
    public function mul($multiplicand);

    /**
     * Return calculate double the value of the current value stored.
     *
     * @return $this
     */
    public function getDouble();

    /**
     * Calculate the mod $int of the current value. No operation if the
     * current value is a point.
     *
     * @param  $int
     * @return $this
     */
    public function mod($int);

    /**
     * Compare the current value with $n.
     *
     * @param  $n
     * @return mixed
     */
    public function cmp($n);

    /**
     * Return the result stored in the class
     * @return string|int|PointInterface
     */
    public function result();
}
