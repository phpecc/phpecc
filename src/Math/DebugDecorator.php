<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Primitives\GeneratorPoint;

/**
 * Debug helper class to trace all calls to math functions along with the provided params and result.
 */
class DebugDecorator implements MathAdapterInterface
{
    /**
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     * @var callable
     */
    private $writer;

    /**
     * @param MathAdapterInterface $adapter
     * @param callable|null        $callback
     */
    public function __construct(MathAdapterInterface $adapter, callable $callback = null)
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
                return var_export($this->adapter->toString($arg), true);
            },
            $args
        );

        if (strpos($func, '::')) {
            list(, $func) = explode('::', $func);
        }

        $this->write($func.'('.implode(', ', $strArgs).')');

        $res = call_user_func_array([ $this->adapter, $func ], $args);

        $this->write(' => ' . var_export($this->adapter->toString($res), true) . PHP_EOL);

        return $res;
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::cmp()
     */
    public function cmp($first, $other)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::mod()
     */
    public function mod($number, $modulus)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::add()
     */
    public function add($augend, $addend)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::sub()
     */
    public function sub($minuend, $subtrahend)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::mul()
     */
    public function mul($multiplier, $multiplicand)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::div()
     */
    public function div($dividend, $divisor)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::pow()
     */
    public function pow($base, $exponent)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::rand()
     */
    public function rand($n)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::bitwiseAnd()
     */
    public function bitwiseAnd($first, $other)
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
    public function toString($value)
    {
        return $this->adapter->toString($value);
    }

    /**
     * {@inheritDoc}
     * @see \Mdanter\Ecc\MathAdapterInterface::hexDec()
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
     * @see \Mdanter\Ecc\MathAdapterInterface::decHex()
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
     * @see \Mdanter\Ecc\MathAdapterInterface::powmod()
     */
    public function powmod($base, $exponent, $modulus)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::isPrime()
     */
    public function isPrime($n)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::nextPrime()
     */
    public function nextPrime($currentPrime)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::inverseMod()
     */
    public function inverseMod($a, $m)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::jacobi()
     */
    public function jacobi($a, $p)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::intToString()
     */
    public function intToString($x)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::stringToInt()
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
     * @see \Mdanter\Ecc\MathAdapterInterface::digestInteger()
     */
    public function digestInteger($m)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::gcd2()
     */
    public function gcd2($a, $m)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::rightShift()
     */
    public function rightShift($number, $positions)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::leftShift()
     */
    public function leftShift($number, $positions)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::bitwiseXor()
     */
    public function bitwiseXor($first, $other)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::baseConvert()
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
     * @see \Mdanter\Ecc\MathAdapterInterface::getEcMath()
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
     * @see \Mdanter\Ecc\MathAdapterInterface::getPrimeFieldArithmetic()
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
     * @see \Mdanter\Ecc\MathAdapterInterface::getModularArithmetic()
     */
    public function getModularArithmetic($modulus)
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
     * @see \Mdanter\Ecc\MathAdapterInterface::getNumberTheory()
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
