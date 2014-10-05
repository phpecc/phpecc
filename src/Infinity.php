<?php

namespace Mdanter\Ecc;

final class Infinity implements PointInterface
{

    private static $instance = null;

    /**
     *
     * @return PointInterface
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {

    }

    public function getCurve()
    {
        return null;
    }

    public function getOrder()
    {
        return null;
    }

    public function getX()
    {
        throw new \BadMethodCallException();
    }

    public function getY()
    {
        throw new \BadMethodCallException();
    }

    public function getDouble()
    {
        return self::$instance;
    }

    public function add(PointInterface $addend)
    {
        return $addend;
    }

    public function cmp(PointInterface $other)
    {
        if ($other == self::$instance || $other instanceof Infinity) {
            return 0;
        }

        return 1;
    }

    public function equals(PointInterface $other)
    {
        return $this->cmp($other) == 0;
    }

    public function mul($multiplier)
    {
        return self::$instance;
    }

    public function __toString()
    {
        return 'infinity';
    }
}
