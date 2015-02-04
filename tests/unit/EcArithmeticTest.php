<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\Point;
use Mdanter\Ecc\Points;
use Mdanter\Ecc\CurveFp;
use Mdanter\Ecc\CurveFpInterface;

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

        $check = Points::infinity();

        for ($i = 0; $i < 8; $i++) {
            $p = $g->mul($i % 7);

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

        $this->assertFalse($p3->equals(Points::infinity()));
        $this->assertEquals($math->mod($p3->getX(), 23), $x3);
        $this->assertEquals($math->mod($p3->getY(), 23), $y3);
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testInfinity(MathAdapterInterface $math)
    {
        $c = new CurveFp(23, 1, 1, $math);
        $g = $c->getPoint(13, 7, 7);

        $check = Points::infinity();

        for ($i = 0; $i < 8; $i++) {
            $p = $g->mul($i % 7);

            $this->assertEquals($check, $p, "$g * $i = $p, expected $check");

            $check = $g->add($check);
        }
    }
}
