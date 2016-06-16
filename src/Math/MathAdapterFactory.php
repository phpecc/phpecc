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

        $adapter = new GmpMath();

        return self::wrapAdapter($adapter, (bool) $debug);
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
