<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Math\MathAdapterFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
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
