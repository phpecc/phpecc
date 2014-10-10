<?php

namespace Mdanter\Ecc;

class Points
{

    /**
     * Returns the special "infinity" point.
     *
     * @return \Mdanter\Ecc\PointInterface
     */
    public static function infinity()
    {
        return Infinity::getInstance();
    }
}
