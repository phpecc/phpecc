<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\MathAdapter;

/**
 * Debug helper class to trace all calls to math functions along with the provided params and result.
 *
 * @author thibaud
 *
 */
class DebugAdapter implements MathAdapter
{

    private $adapter;

    private $writer;

    public function __construct(MathAdapter $adapter, $callback)
    {
        $this->adapter = $adapter;
        $this->writer = $callback;
    }

    private function writeln($message)
    {
        call_user_func($this->writer, $message . PHP_EOL);
    }

    private function write($message)
    {
        call_user_func($this->writer, $message);
    }

    private function call($func, $args)
    {
        $strArgs = array_map(function ($arg) {
            return var_export($this->adapter->toString($arg), true);
        }, $args);

        if (strpos($func, '::')) {
            list(, $func) = explode('::', $func);
        }

        $res = call_user_func_array([ $this->adapter, $func ], $args);

        $this->writeln($func . '(' . implode(', ', $strArgs) . ') => ' . var_export($this->adapter->toString($res), true));

        return $res;
    }

    public function cmp($first, $other)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function mod($number, $modulus)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function add($augend, $addend)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function sub($minuend, $subtrahend)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function mul($multiplier, $multiplicand)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    /**
     *
     * @param integer $divisor
     */
    function div($dividend, $divisor)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    /**
     *
     * @param integer $exponent
     */
    function pow($base, $exponent)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function rand($n)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function bitwiseAnd($first, $other)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function toString($value)
    {
        return $this->adapter->toString($value);
    }

    /**
     *
     * @param string $hexString
     */
    function hexDec($hexString)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function decHex($decString)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function powmod($base, $exponent, $modulus)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    /**
     *
     * @return boolean
     */
    function isPrime($n)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function nextPrime($currentPrime)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function inverseMod($a, $m)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function jacobi($a, $p)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    /**
     *
     * @return string|null
     */
    function intToString($x)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function stringToInt($s)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function digestInteger($m)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }

    function gcd2($a, $m)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func([ $this, 'call' ], $func, $args);
    }
}
