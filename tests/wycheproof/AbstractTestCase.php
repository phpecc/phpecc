<?php
declare(strict_types=1);

namespace Mdanter\Ecc\WycheProof;

use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;
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

    protected function getCurvesList(): array
    {
        return [
            NistCurve::NAME_P192,
            NistCurve::NAME_P224,
            NistCurve::NAME_P256,
            NistCurve::NAME_P384,
            NistCurve::NAME_P521,
            SecgCurve::NAME_SECP_112R1,
            SecgCurve::NAME_SECP_192K1,
            SecgCurve::NAME_SECP_256K1,
            SecgCurve::NAME_SECP_256R1,
            SecgCurve::NAME_SECP_384R1,
        ];
    }

    public function importFile(string $name): string
    {
        $contents = file_get_contents("tests/{$name}");
        if (!$contents) {
            throw new \InvalidArgumentException("Failed to read test fixture file tests/$name");
        }
        return $contents;
    }

    public function getAdapters()
    {
        return $this->_getAdapters();
    }
}
