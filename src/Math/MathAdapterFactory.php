<?php

namespace Mdanter\Ecc\Math;

class MathAdapterFactory
{
    private static $forcedAdapter = null;

    /**
     * @param GmpMathInterface $adapter
     */
    public static function forceAdapter(GmpMathInterface $adapter = null)
    {
        self::$forcedAdapter = $adapter;
    }

    /**
     * @param bool $debug
     * @return DebugDecorator|GmpMathInterface|null
     */
    public static function getAdapter($debug = false)
    {
        if (self::$forcedAdapter !== null) {
            return self::$forcedAdapter;
        }

        $adapterClass = self::getAdapterClass();
        $adapter = new $adapterClass();

        return self::wrapAdapter($adapter, (bool) $debug);
    }

    /**
     * @return string
     */
    private static function getAdapterClass()
    {
        if (self::canLoad('gmp')) {
            return GmpMath::class;
        }

        throw new \RuntimeException('Please install GMP extension.');
    }

    /**
     * @param $target
     * @return bool
     */
    private static function canLoad($target)
    {
        return extension_loaded($target);
    }

    /**
     * @param GmpMathInterface $adapter
     * @param bool $debug
     * @return DebugDecorator|GmpMathInterface
     */
    private static function wrapAdapter(GmpMathInterface $adapter, $debug)
    {
        if ($debug === true) {
            return new DebugDecorator($adapter);
        }

        return $adapter;
    }
}
