<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Math\BcMath;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{

    protected function _getAdapters(array $extra = null)
    {
        if ($extra == null) {
            return [
            [ new Gmp() ],
            [ new BcMath() ]
            ];
        }

        $adapters = $this->_getAdapters(null);
        $result = [];

        foreach ($adapters as $adapter) {
            foreach ($extra as $value) {
                $result[] = array_merge($adapter, $value);
            }
        }

        return $result;
    }

    public function getAdapters()
    {
        return $this->_getAdapters();
    }
}
