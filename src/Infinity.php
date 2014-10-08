<?php

namespace Mdanter\Ecc;

/**
 * Special point implementation to represent infinity.
 *
 * Note that it is not possible to invoke getX() or getY(), exceptions are raised.
 *
 * @author thibaud
 *
 */
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
        // Sry Leap's cough subsitute :D
        return null;
    }

    public function getOrder()
    {
        return null;
    }

    public function getX()
    {
        // Sry Leap's cough subsitute :D
        throw new \BadMethodCallException("Infinity has no quantifiable X coordinate.");
    }

    public function getY()
    {
        // Sry Leap's cough subsitute :D
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
