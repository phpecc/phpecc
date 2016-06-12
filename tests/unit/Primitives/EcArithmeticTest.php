<?php

namespace Mdanter\Ecc\Tests\Primitives;

use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Primitives\CurveParameters;
use Mdanter\Ecc\Primitives\CurveFp;
use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Tests\AbstractTestCase;

class EcArithmeticTest extends AbstractTestCase
{
    private function add(GmpMathInterface $math, CurveFpInterface $c, $x1, $y1, $x2, $y2, $x3, $y3)
    {
        $p1 = $c->getPoint(gmp_init($x1, 10), gmp_init($y1, 10));
        $p2 = $c->getPoint(gmp_init($x2, 10), gmp_init($y2, 10));

        $p3 = $p1->add($p2);

        $this->assertEquals($x3, $math->toString($math->mod($p3->getX(), gmp_init(23, 10))));
        $this->assertEquals($y3, $math->toString($math->mod($p3->getY(), gmp_init(23, 10))));
    }

    /**
     *
     * @dataProvider getAdapters
     * @testdox Test point additions yield expected results
     */
    public function testAdditions(GmpMathInterface $math)
    {
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $math);

        $this->add($math, $curve, 3, 10, 9, 7, 17, 20);
    }

    /**
     *
     * @dataProvider getAdapters
     * @testdox Test point additions are associative
     */
    public function testAdditionCommutativity(GmpMathInterface $math)
    {
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $curve = new CurveFp($parameters, $math);

        $p1 = $curve->getPoint(gmp_init(3, 10), gmp_init(10, 10));
        $p2 = $curve->getPoint(gmp_init(9, 10), gmp_init(7, 10));

        $p3a = $p1->add($p2);
        $p4a = $p2->add($p1);

        $this->assertTrue($p3a->equals($p4a));

        $c = new CurveFp($parameters, $math);
        $g = $c->getPoint(gmp_init(13, 10), gmp_init(7, 10), gmp_init(7, 10));
        $check = $c->getInfinity();

        for ($i = 0; $i < 8; $i++) {
            $a = $check->add($g);
            $b = $g->add($check);

            $this->assertTrue($a->equals($b), "$a == $b ? with $check and $g");

            $check = $a;
        }
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testDouble(GmpMathInterface $math)
    {
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $c = new CurveFp($parameters, $math);

        $x1 = 3;
        $y1 = 10;
        $x3 = 7;
        $y3 = 12;

        // expect that on curve c, (x1, y1) + (x2, y2) = (x3, y3)
        $p1 = $c->getPoint(gmp_init($x1, 10), gmp_init($y1, 10));
        $p3 = $p1->getDouble();

        $this->assertEquals($x3, $math->toString($math->mod($p3->getX(), gmp_init(23, 10))));
        $this->assertEquals($y3, $math->toString($math->mod($p3->getY(), gmp_init(23, 10))));
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testAddDouble(GmpMathInterface $math)
    {
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $c = new CurveFp($parameters, $math);

        $this->add($math, $c, 3, 10, 3, 10, 7, 12);
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testMultiply(GmpMathInterface $math)
    {
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $c = new CurveFp($parameters, $math);

        $x1 = 3;
        $y1 = 10;
        $m = gmp_init(2);
        $x3 = 7;
        $y3 = 12;

        $p1 = $c->getPoint(gmp_init($x1, 10), gmp_init($y1, 10));
        $p3 = $p1->mul($m);

        $this->assertFalse($p3->isInfinity());
        $this->assertEquals($x3, $math->toString($math->mod($p3->getX(), gmp_init(23, 10))));
        $this->assertEquals($y3, $math->toString($math->mod($p3->getY(), gmp_init(23, 10))));
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
    public function testMultiply2(GmpMathInterface $math, $p, $a, $b, $x, $y, $m, $ex, $ey)
    {
        $p = gmp_init($p, 10);
        $parameters = new CurveParameters(32, $p, gmp_init($a, 10), gmp_init($b, 10));
        $c = new CurveFp($parameters, $math);

        $p1 = $c->getPoint(gmp_init($x, 10), gmp_init($y, 10));
        $p3 = $p1->mul(gmp_init($m, 10));

        $this->assertFalse($p3->isInfinity());

        $this->assertEquals($ex, $math->toString($math->mod($p3->getX(), $p)));
        $this->assertEquals($ey, $math->toString($math->mod($p3->getY(), $p)));
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testMultiplyAssociative(GmpMathInterface $math)
    {
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $c = new CurveFp($parameters, $math);

        $g = $c->getPoint(gmp_init(13, 10), gmp_init(7, 10), null);

        $a = $g->mul(gmp_init('1234564564564564564564564564564564646', 10))->mul(gmp_init(10, 10));
        $b = $g->mul(gmp_init(10, 10))->mul(gmp_init('1234564564564564564564564564564564646', 10));

        $this->assertTrue($a->equals($b));
    }

    /**
     *
     * @dataProvider getAdapters
     */
    public function testInfinity(GmpMathInterface $math)
    {
        $parameters = new CurveParameters(32, gmp_init(23, 10), gmp_init(1, 10), gmp_init(1, 10));
        $c = new CurveFp($parameters, $math);

        $g = $c->getPoint(gmp_init(13, 10), gmp_init(7, 10), null);

        $check = $c->getInfinity();

        for ($i = 0; $i < 8; $i++) {
            $mul = gmp_init($i % 7, 10);
            $p = $g->mul($mul);
            $this->assertTrue($check->equals($p), "$g * $p, expected $check  ");
            $check = $g->add($check);
        }
    }
}
