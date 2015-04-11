<?php

namespace Mdanter\Ecc\Tests\Primitives;

use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Primitives\Point;
use Mdanter\Ecc\Primitives\CurveFp;
use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Math\MathAdapterFactory;
use Mdanter\Ecc\Tests\AbstractTestCase;

class EcArithmeticTest extends AbstractTestCase
{
    private function add(MathAdapterInterface $math, CurveFpInterface $c, $x1, $y1, $x2, $y2, $x3, $y3)
    {
        $p1 = $c->getPoint($x1, $y1);
        $p2 = $c->getPoint($x2, $y2);

        $p3 = $p1->add($p2);

        $this->assertEquals($math->mod($p3->getX(), 23), $x3);
        $this->assertEquals($math->mod($p3->getY(), 23), $y3);
    }

    /**
     *
     * @dataProvider getAdapters
     * @testdox Test point additions yield expected results
     */
    public function testAdditions(MathAdapterInterface $math)
    {
        $curve = new CurveFp(23, 1, 1, $math);

        $this->add($math, $curve, 3, 10, 9, 7, 17, 20);
    }

    /**
     *
     * @dataProvider getAdapters
     * @testdox Test point additions are associative
     */
    public function testAdditionCommutativity(MathAdapterInterface $math)
    {
        $curve = new CurveFp(23, 1, 1, $math);

        $p1 = $curve->getPoint(3, 10);
        $p2 = $curve->getPoint(9, 7);

        $p3a = $p1->add($p2);
        $p4a = $p2->add($p1);

        $this->assertTrue($p3a == $p4a);

        $c = new CurveFp(23, 1, 1, $math);
        $g = $c->getPoint(13, 7, 7);
        $check = $c->getInfinity();

        for ($i = 0; $i < 8; $i++) {
            $a = $check->add($g);
            $b = $g->add($check);

            $this->assertTrue($a == $b, "$a == $b ? with $check and $g");

            $check = $a;
        }
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testDouble(MathAdapterInterface $math)
    {
        $c = new CurveFp(23, 1, 1, $math);
        $x1 = 3;
        $y1 = 10;
        $x3 = 7;
        $y3 = 12;

        // expect that on curve c, (x1, y1) + (x2, y2) = (x3, y3)
        $p1 = $c->getPoint($x1, $y1);
        $p3 = $p1->getDouble();

        $this->assertEquals($math->mod($p3->getX(), 23), $x3);
        $this->assertEquals($math->mod($p3->getY(), 23), $y3);
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testAddDouble(MathAdapterInterface $math)
    {
        $c = new CurveFp(23, 1, 1, $math);

        $this->add($math, $c, 3, 10, 3, 10, 7, 12);
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testMultiply(MathAdapterInterface $math)
    {
        $c = new CurveFp(23, 1, 1, $math);
        $x1 = 3;
        $y1 = 10;
        $m = 2;
        $x3 = 7;
        $y3 = 12;

        $p1 = $c->getPoint($x1, $y1);
        $p3 = $p1->mul($m);

        $this->assertFalse($p3->isInfinity());
        $this->assertEquals($x3, $math->mod($p3->getX(), 23));
        $this->assertEquals($y3, $math->mod($p3->getY(), 23));
    }

    public function getMultAdapters()
    {
        // https://www.certicom.com/index.php/52-the-elliptic-curve-discrete-logarithm-problem
        return $this->_getAdapters([
            [ 23, 9, 17, 16, 5, 9, 4, 5 ],
            [ 23, 9, 17, 16, 5, 8, 12, 17 ],
            [ 23, 9, 17, 16, 5, 7, 8, 7 ],
            [ 23, 9, 17, 16, 5, 6, 7, 3 ],
            [ 23, 9, 17, 16, 5, 5, 13, 10 ],
            [ 23, 9, 17, 16, 5, 4, 19, 20 ],
            [ 23, 9, 17, 16, 5, 3, 14, 14 ],
            [ 23, 9, 17, 16, 5, 2, 20, 20 ],
            [ 23, 9, 17, 16, 5, 1, 16, 5 ],
            [ 2111, 20, 13, 3, 10, 57, 470, 1757]
        ]);
    }

    /**
     *
     * @dataProvider getMultAdapters
     */
    public function testMultiply2(MathAdapterInterface $math, $p, $a, $b, $x, $y, $m, $ex, $ey)
    {
        $c = new CurveFp($p, $a, $b, $math);

        $p1 = $c->getPoint($x, $y);
        $p3 = $p1->mul($m);

        $this->assertFalse($p3->isInfinity());

        $this->assertEquals($ex, $math->mod($p3->getX(), $p));
        $this->assertEquals($ey, $math->mod($p3->getY(), $p));
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testMultiplyAssociative(MathAdapterInterface $math)
    {
        $c = new CurveFp(23, 1, 1, $math);
        $g = $c->getPoint(13, 7, null);

        $a = $g->mul('1234564564564564564564564564564564646')->mul(10);
        $b = $g->mul(10)->mul('1234564564564564564564564564564564646');

        $this->assertTrue($a->equals($b));
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testInfinity(MathAdapterInterface $math)
    {
        $c = new CurveFp(23, 1, 1, $math);
        $g = $c->getPoint(13, 7, 7);

        $check = $c->getInfinity();

        for ($i = 0; $i < 8; $i++) {
            $mul = $i % 7;
            $p = $g->mul($mul);

            $this->assertTrue($check->equals($p), "$g * $mul = $p, expected $check");

            $check = $g->add($check);
        }
    }
}
