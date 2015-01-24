<?php

namespace Mdanter\Ecc;

class UnsafePoint implements PointInterface
{

    private $curve;

    private $adapter;

    private $x;

    private $y;

    private $order;

    private $infinity = false;

    public function __construct(MathAdapterInterface $adapter, CurveFpInterface $curve, $x, $y, $order, $infinity = false)
    {
        $this->adapter  = $adapter;
        $this->curve    = $curve;
        $this->x        = (string) $x;
        $this->y        = (string) $y;
        $this->order    = $order;
        $this->infinity = (bool) $infinity;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function isInfinity()
    {
        return (bool) $this->infinity;
    }

    /*
     * (non-PHPdoc) @see \Mdanter\Ecc\PointInterface::getCurve()
    */
    public function getCurve()
    {
        return $this->curve;
    }

    /*
     * (non-PHPdoc) @see \Mdanter\Ecc\PointInterface::getOrder()
    */
    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = (string) $order;
    }

    /*
     * (non-PHPdoc) @see \Mdanter\Ecc\PointInterface::getX()
    */
    public function getX()
    {
        return $this->x;
    }

    public function setX($x)
    {
        $this->x = (string) $x;
    }

    /*
     * (non-PHPdoc) @see \Mdanter\Ecc\PointInterface::getY()
    */
    public function getY()
    {
        return $this->y;
    }

    public function setY($y)
    {
        $this->y = (string) $y;
    }

    /*
     * (non-PHPdoc) @see \Mdanter\Ecc\PointInterface::add()
     */
    public function add(PointInterface $addend)
    {
        if (! $this->curve->equals($addend->getCurve())) {
            throw new \RuntimeException("The Elliptic Curves do not match.");
        }

        if ($addend->isInfinity()) {
            return $this;
        }

        if ($this->isInfinity()) {
            return $addend;
        }

        $math = $this->adapter;

        if ($math->mod($math->cmp($this->x, $addend->getX()), $this->curve->getPrime()) == 0) {
            if ($math->mod($math->add($this->y, $addend->getY()), $this->curve->getPrime()) == 0) {
                return new self($this->adapter, $this->curve, 0, 0, 0, true);
            } else {
                return $this->getDouble();
            }
        }

        $p = $this->curve->getPrime();
        $l = $math->mod($math->mul($math->sub($addend->getY(), $this->y), $math->inverseMod($math->sub($addend->getX(), $this->x), $p)), $p);

        $x3 = $math->mod($math->sub($math->sub($math->pow($l, 2), $this->x), $addend->getX()), $p);
        $y3 = $math->mod($math->sub($math->mul($l, $math->sub($this->x, $x3)), $this->y), $p);

        if ($math->cmp(0, $y3) > 0) {
            $y3 = $math->add($p, $y3);
        }

        return new self($this->adapter, $this->curve, $x3, $y3, $this->order, false);
    }

    /*
     * (non-PHPdoc) @see \Mdanter\Ecc\PointInterface::cmp()
     */
    public function cmp(PointInterface $other)
    {
        if ($other->isInfinity() && $this->isInfinity()) {
            return 0;
        }

        if ($other->isInfinity() || $this->isInfinity()) {
            return 1;
        }

        $math = $this->adapter;
        $equal = ($math->cmp($this->x, $other->getX()) == 0);
        $equal &= ($math->cmp($this->y, $other->getY()) == 0);
        $equal &= $this->isInfinity() == $other->isInfinity();
        $equal &= $this->curve->equals($other->getCurve());

        if ($equal) {
            return 0;
        }

        return 1;
    }

    /*
     * (non-PHPdoc) @see \Mdanter\Ecc\PointInterface::equals()
     */
    public function equals(PointInterface $other)
    {
        return $this->cmp($other) == 0;
    }

    /*
     * (non-PHPdoc) @see \Mdanter\Ecc\PointInterface::mul()
     */
    public function mul($n)
    {
        if ($this->order != null) {
            $n = $this->adapter->mod($n, $this->order);
        }

        if ($this->adapter->cmp($n, 0) == 0) {
            return new self($this->adapter, $this->curve, 0, 0, 0, true);
        }

        $r = [
            new self($this->adapter, $this->curve, 0, 0, 0, true),
            $this
        ];

        $k = (strlen($n) * 8);

        for ($i = $k; $i > 0; $i--) {
            // Value of n[i]
            $j = $this->getBitAt($n, $i - 1);
            $b = $this->adapter->bitwiseXor($j, 1);

            $this->cswap($r[0], $r[1], $k - 1, $b);

            $r[0] = $r[0]->add($r[1]);
            $r[1] = $r[1]->getDouble();

            $this->cswap($r[0], $r[1], $k, $b);
        }

        return $r[0];
    }

    private function cswap(self $a, self $b, $size, $cond)
    {
        $this->cswapValue($a->x, $b->x, $size, $cond);
        $this->cswapValue($a->y, $b->y, $size, $cond);
        $this->cswapValue($a->order, $b->order, $size, $cond);
        $this->cswapValue($a->infinity, $b->infinity, $size, $cond);
    }

    private function cswapValue(& $a, & $b, $size, $cond)
    {
        $len = max(strlen($a), strlen($b), 0);
        $a = str_pad($a, $len, '0', STR_PAD_LEFT);
        $b = str_pad($b, $len, '0', STR_PAD_LEFT);

        $mask = $this->adapter->sub(0, $cond);

        for ($i = 0; $i < $size; $i++) {
            $ba = $this->getBitAt($a, $i);
            $bb = $this->getBitAt($b, $i);

            $t = $this->adapter->bitwiseAnd($this->adapter->bitwiseXor($ba, $bb), $mask);

            $a = $this->setBitAt($a, $i, $this->adapter->bitwiseXor($t, $ba));
            $b = $this->setBitAt($b, $i, $this->adapter->bitwiseXor($t, $bb));
        }
    }

    private function getBitAt($value, $position)
    {
        $value = $this->adapter->rightShift($value, $position);

        return $this->adapter->bitwiseAnd($value, '1');
    }

    private function setBitAt($value, $position, $bitValue)
    {
        $mask = $this->adapter->leftShift(1, $position);
        $bitValue = $this->adapter->leftShift($bitValue, $position);

        return $this->adapter->bitwiseXor(
            $value,
            $this->adapter->bitwiseAnd(
                $this->adapter->bitwiseXor($value, $bitValue),
                $mask
        ));
    }

    /*
     * (non-PHPdoc) @see \Mdanter\Ecc\PointInterface::getDouble()
     */
    public function getDouble()
    {
        if ($this->isInfinity()) {
            return $this->curve->getInfinity();
        }

        $math = $this->adapter;

        $p = $this->curve->getPrime();
        $a = $this->curve->getA();

        $inverse = $math->inverseMod($math->mul(2, $this->y), $p);
        $threeX2 = $math->mul(3, $math->pow($this->x, 2));

        $l  = $math->mod($math->mul($math->add($threeX2, $a), $inverse), $p);
        $x3 = $math->mod($math->sub($math->pow($l, 2), $math->mul(2, $this->x)), $p);
        $y3 = $math->mod($math->sub($math->mul($l, $math->sub($this->x, $x3)), $this->y), $p);

        if ($math->cmp(0, $y3) > 0) {
            $y3 = $math->add($p, $y3);
        }

        return new self($this->adapter, $this->curve, $x3, $y3, $this->order);
    }

    /*
     * (non-PHPdoc) @see \Mdanter\Ecc\PointInterface::__toString()
     */
    public function __toString()
    {
        if ($this->infinity) {
            return '(infinity)';
        }

        return "(" . $this->adapter->toString($this->x) . "," . $this->adapter->toString($this->y) . ")";
    }

    public function __debugInfo()
    {
        if ($this->infinity) {
            return [
                'x' => 'inf',
                'y' => 'inf',
                'z' => 'inf',
                'curve' => $this->curve
            ];
        }

        return [
            'x' => (string) $this->x,
            'y' => (string) $this->y,
            'z' => (string) $this->order,
            'curve' => $this->curve
        ];
    }
}