<?php

namespace Mdanter\Ecc\Math;

class MathAdapterFactory
{
    private static $forcedAdapter = null;

    /**
     * @param MathAdapterInterface $adapter
     */
    public static function forceAdapter(MathAdapterInterface $adapter = null)
    {
        self::$forcedAdapter = $adapter;
    }

    /**
     * @param bool $debug
     * @return DebugDecorator|MathAdapterInterface|null
     */
    public static function getAdapter($debug = false)
    {
        if (self::$forcedAdapter !== null) {
            return self::$forcedAdapter;
        }

        $adapter = null;
        $adapterClass = self::getAdapterClass();

        $adapter = new $adapterClass();

        return self::wrapAdapter($adapter, (bool) $debug);
    }

    /**
     * @param bool $debug
     * @return DebugDecorator|MathAdapterInterface
     */
    public static function getGmpAdapter($debug = false)
    {
        if (self::canLoad('gmp')) {
            return self::wrapAdapter(new Gmp(), $debug);
        }

        throw new \RuntimeException('Please install GMP extension.');
    }

    /**
     * @return string
     */
    private static function getAdapterClass()
    {
        if (self::canLoad('gmp')) {
            return '\Mdanter\Ecc\Math\Gmp';
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
     * @param MathAdapterInterface $adapter
     * @param $debug
     * @return DebugDecorator|MathAdapterInterface
     */
    private static function wrapAdapter(MathAdapterInterface $adapter, $debug)
    {
        if ($debug === true) {
            return new DebugDecorator($adapter);
        }

        return $adapter;
    }
}
