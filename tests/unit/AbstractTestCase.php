<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Math\BcMath;
use Mdanter\Ecc\Math\MathAdapterFactory;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    protected function _getAdapters(array $extra = null)
    {
        if (! defined('PHPUNIT_DEBUG')) {
            define('PHPUNIT_DEBUG', false);
        }

        switch (MATH_LIB) {
            case 'bcmath':
                $adapter = MathAdapterFactory::getBcMathAdapter(PHPUNIT_DEBUG);
                break;
            case 'gmp':
            default:
                $adapter = MathAdapterFactory::getGmpAdapter(PHPUNIT_DEBUG);
        }

        if ($extra == null) {
            return array(
                array($adapter),
            );
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
