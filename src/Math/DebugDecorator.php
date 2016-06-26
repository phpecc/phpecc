<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Primitives\GeneratorPoint;

/**
 * Debug helper class to trace all calls to math functions along with the provided params and result.
 */
class DebugDecorator implements GmpMathInterface
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @var callable
     */
    private $writer;

    /**
     * @param GmpMathInterface     $adapter
     * @param callable|null        $callback
     */
    public function __construct(GmpMathInterface $adapter, callable $callback = null)
    {
        $this->adapter = $adapter;
        $this->writer = $callback ?: function ($message) {
            echo $message;
        };
    }

    /**
     *
     * @param string $message
     */
    private function write($message)
    {
        call_user_func($this->writer, $message);
    }

    /**
     *
     * @param  string $func
     * @param  array  $args
     * @return mixed
     */
    private function call($func, $args)
    {
        $strArgs = array_map(
            function ($arg) {
                if ($arg instanceof \GMP) {
                    return var_export($this->adapter->toString($arg), true);
                } else {
                    return var_export($arg, true);
                }
            },
            $args
        );

        if (strpos($func, '::')) {
            list(, $func) = explode('::', $func);
        }

        $this->write($func.'('.implode(', ', $strArgs).')');

        $res = call_user_func_array([ $this->adapter, $func ], $args);

        if ($res instanceof \GMP) {
            $this->write(' => ' . var_export($this->adapter->toString($res), true) . PHP_EOL);
        } else {
            $this->write(' => ' . var_export($res, true) . PHP_EOL);
        }

        return $res;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::cmp()
     */
    public function cmp(\GMP $first, \GMP $other)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }


    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::cmp()
     */
    public function equals(\GMP $first, \GMP $other)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
                $this,
                'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::mod()
     */
    public function mod(\GMP $number, \GMP $modulus)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::add()
     */
    public function add(\GMP $augend, \GMP $addend)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::sub()
     */
    public function sub(\GMP $minuend, \GMP $subtrahend)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::mul()
     */
    public function mul(\GMP $multiplier, \GMP $multiplicand)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::div()
     */
    public function div(\GMP $dividend, \GMP $divisor)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::pow()
     */
    public function pow(\GMP $base, $exponent)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::bitwiseAnd()
     */
    public function bitwiseAnd(\GMP $first, \GMP $other)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapter::toString()
     */
    public function toString(\GMP $value)
    {
        return $this->adapter->toString($value);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::hexDec()
     */
    public function hexDec($hexString)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::decHex()
     */
    public function decHex($decString)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::powmod()
     */
    public function powmod(\GMP $base, \GMP $exponent, \GMP $modulus)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::isPrime()
     */
    public function isPrime(\GMP $n)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::nextPrime()
     */
    public function nextPrime(\GMP $currentPrime)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::inverseMod()
     */
    public function inverseMod(\GMP $a, \GMP $m)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::jacobi()
     */
    public function jacobi(\GMP $a, \GMP $p)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::intToString()
     */
    public function intToString(\GMP $x)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::stringToInt()
     */
    public function stringToInt($s)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::digestInteger()
     */
    public function digestInteger(\GMP $m)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::gcd2()
     */
    public function gcd2(\GMP $a, \GMP $m)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::rightShift()
     */
    public function rightShift(\GMP $number, $positions)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::leftShift()
     */
    public function leftShift(\GMP $number, $positions)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call',
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::bitwiseXor()
     */
    public function bitwiseXor(\GMP $first, \GMP $other)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call'
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::baseConvert()
     */
    public function baseConvert($value, $fromBase, $toBase)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call'
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::getEcMath()
     */
    public function getEcMath(GeneratorPoint $generatorPoint, $input)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call'
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::getPrimeFieldArithmetic()
     */
    public function getPrimeFieldArithmetic(CurveFpInterface $curve)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call'
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::getModularArithmetic()
     */
    public function getModularArithmetic(\GMP $modulus)
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
                $this,
                'call'
            ),
            $func,
            $args
        );
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\GmpMathInterface::getNumberTheory()
     */
    public function getNumberTheory()
    {
        $func = __METHOD__;
        $args = func_get_args();

        return call_user_func(
            array(
            $this,
            'call'
            ),
            $func,
            $args
        );
    }
}
