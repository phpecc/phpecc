<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Math\MathAdapterFactory;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    public $classPointInterface = 'Mdanter\Ecc\Primitives\PointInterface';

    /**
     * @var string
     */
    public $classCurveFpInterface = 'Mdanter\Ecc\Primitives\CurveFpInterface';

    /**
     * @var string
     */
    public $classRngInterface = '\Mdanter\Ecc\Random\RandomNumberGeneratorInterface';

    /**
     * @param array $extra
     * @return array
     */
    protected function _getAdapters(array $extra = null)
    {
        if (! defined('PHPUNIT_DEBUG')) {
            define('PHPUNIT_DEBUG', false);
        }

        switch (MATH_LIB) {
            case 'gmp':
            default:
                $adapter = MathAdapterFactory::getAdapter(PHPUNIT_DEBUG);
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
