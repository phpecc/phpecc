<?php

namespace Mdanter\Ecc\Math;

/***********************************************************************
     * Copyright (C) 2012 Matyas Danter
     *
     * Permission is hereby granted, free of charge, to any person obtaining
     * a copy of this software and associated documentation files (the "Software"),
     * to deal in the Software without restriction, including without limitation
     * the rights to use, copy, modify, merge, publish, distribute, sublicense,
     * and/or sell copies of the Software, and to permit persons to whom the
     * Software is furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included
     * in all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
     * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
     * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
     * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
     * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
     * OTHER DEALINGS IN THE SOFTWARE.
     ************************************************************************/

/**
 * Implementation of some number theoretic algorithms
 *
 * @author Matyas Danter
 */

/**
 * Rewritten to take a MathAdaptor to handle different environments. Has
 * some desireable functions for public key compression/recovery.
 */
class NumberTheory
{
    /**
     * @var GmpMathInterface
     */
    protected $adapter;

    /**
     * @param GmpMathInterface $adapter
     */
    public function __construct(GmpMathInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param $poly
     * @param $polymod
     * @param $p
     * @return array
     */
    public function polynomialReduceMod($poly, $polymod, $p)
    {
        $count_polymod = count($polymod);
        if (end($polymod) == 1 && $count_polymod > 1) {
            while (count($poly) >= $count_polymod) {
                if (end($poly) != 0) {
                    for ($i = 2; $i < $count_polymod + 1; $i++) {
                        $poly[count($poly) - $i] =
                            $this->adapter->mod(
                                $this->adapter->sub(
                                    $poly[count($poly) - $i],
                                    $this->adapter->mul(
                                        end($poly),
                                        $polymod[$count_polymod - $i]
                                    )
                                ),
                                $p
                            );
                    }
                }

                $poly = array_slice($poly, 0, count($poly) - 1);
            }

            return $poly;
        }

        throw new \InvalidArgumentException('Unable to calculate polynomialReduceMod');
    }

    /**
     * @param $m1
     * @param $m2
     * @param $polymod
     * @param $p
     * @return array
     */
    public function polynomialMultiplyMod($m1, $m2, $polymod, $p)
    {
        $prod = array();
        $cm1 = count($m1);
        $cm2 = count($m2);

        for ($i = 0; $i < $cm1; $i++) {
            for ($j = 0; $j < $cm2; $j++) {
                $index = $i + $j;
                if (!isset($prod[$index])) {
                    $prod[$index] = 0;
                }
                $prod[$index] =
                    $this->adapter->mod(
                        $this->adapter->add(
                            $prod[$index],
                            $this->adapter->mul(
                                $m1[$i],
                                $m2[$j]
                            )
                        ),
                        $p
                    );
            }
        }

        return $this->polynomialReduceMod($prod, $polymod, $p);
    }

    /**
     * @param array $base
     * @param \GMP $exponent
     * @param array $polymod
     * @param \GMP $p
     * @return array|int
     */
    public function polynomialPowMod($base, \GMP $exponent, $polymod, \GMP $p)
    {
        $zero = gmp_init(0, 10);
        $one = gmp_init(1, 10);
        $two = gmp_init(2, 10);

        if ($this->adapter->cmp($exponent, $p) < 0) {
            if ($this->adapter->cmp($exponent, $zero) == 0) {
                return 1;
            }

            $G = $base;
            $k = $exponent;

            if ($this->adapter->cmp($this->adapter->mod($k, $two), $one) == 0) {
                $s = $G;
            } else {
                $s = array(1);
            }

            while ($this->adapter->cmp($k, $one) > 0) {
                $k = $this->adapter->div($k, $two);

                $G = $this->polynomialMultiplyMod($G, $G, $polymod, $p);
                if ($this->adapter->mod($k, $two) == 1) {
                    $s = $this->polynomialMultiplyMod($G, $s, $polymod, $p);
                }
            }

            return $s;
        }

        throw new \InvalidArgumentException('Unable to calculate polynomialPowMod');

    }

    /**
     * @param \GMP $a
     * @param \GMP $p
     * @return \GMP
     */
    public function squareRootModP(\GMP $a, \GMP $p)
    {
        $four = gmp_init(4, 10);
        $eight = gmp_init(8, 10);
        if (0 <= $a && $a < $p && 1 < $p) {
            if ($a == 0) {
                return 0;
            }

            if ($p == 2) {
                return $a;
            }
            $jac = $this->adapter->jacobi($a, $p);

            if ($jac == -1) {
                throw new \LogicException(gmp_strval($a, 10)." has no square root modulo ".gmp_strval($p, 10));
            }

            if ($this->adapter->mod($p, $four) == 3) {
                return $this->adapter->powmod($a, $this->adapter->div($this->adapter->add($p, gmp_init(1, 10)), $four), $p);
            }

            if ($this->adapter->mod($p, $eight) == 5) {
                $d = $this->adapter->powmod($a, $this->adapter->div($this->adapter->sub($p, gmp_init(1, 10)), $four), $p);
                if ($d == 1) {
                    return $this->adapter->powmod($a, $this->adapter->div($this->adapter->add($p, gmp_init(3, 10)), $eight), $p);
                }
                if ($d == $p - 1) {
                    return $this->adapter->mod(
                        $this->adapter->mul(
                            $this->adapter->mul(
                                gmp_init(2, 10),
                                $a
                            ),
                            $this->adapter->powmod(
                                $this->adapter->mul(
                                    $four,
                                    $a
                                ),
                                $this->adapter->div(
                                    $this->adapter->sub(
                                        $p,
                                        gmp_init(5, 10)
                                    ),
                                    $eight
                                ),
                                $p
                            )
                        ),
                        $p
                    );
                }
                //shouldn't get here
            }

            for ($b = 2; $b < $p; $b++) {
                if ($this->adapter->jacobi(
                    $this->adapter->sub(
                        $this->adapter->mul(gmp_init($b, 10), gmp_init($b, 10)),
                        $this->adapter->mul($four, $a)
                    ),
                    $p
                ) == -1
                ) {
                    $f = array($a, -$b, 1);

                    $ff = $this->polynomialPowMod(
                        array(0, 1),
                        $this->adapter->div(
                            $this->adapter->add(
                                $p,
                                gmp_init(1, 10)
                            ),
                            gmp_init(2, 10)
                        ),
                        $f,
                        $p
                    );

                    if ($ff[1] == 0) {
                        return $ff[0];
                    }
                    // if we got here no b was found
                }
            }
        }

        throw new \InvalidArgumentException('Unable to calculate square root mod p!');

    }
}
