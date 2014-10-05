<?php

namespace Mdanter\Ecc;

class Points {

    /**
     * @return \Mdanter\Ecc\PointInterface
     */
    public static function infinity()
    {
        return Infinity::getInstance();
    }

}
