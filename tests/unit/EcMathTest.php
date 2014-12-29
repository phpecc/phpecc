<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Math\BcMath;
use Mdanter\Ecc\MathAdapter;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\EcMath;
use Mdanter\Ecc\Point;
use Mdanter\Ecc\Points;
use Mdanter\Ecc\CurveFp;
use Mdanter\Ecc\CurveFpInterface;
use Mdanter\Ecc\Math\DebugDecorator;

class EcMathTest extends \PHPUnit_Framework_TestCase
{

    public function getAdapters()
    {
        return [
            [ new DebugDecorator(new Gmp(), function ($msg) { /*echo $msg;*/ }) ],
            [ new DebugDecorator(new BcMath(), function ($msg) { /*echo $msg;*/ }) ]
        ];
    }

    /**
     *
     * @dataProvider getAdapters
     * @testdox Test point additions yield expected results
     */
    public function testCreateNew(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $ecInt = new EcMath(1, $G, $math);
        $this->assertInstanceOf('Mdanter\Ecc\EcMath', $ecInt);
        $this->assertSame(1, $ecInt->result());

        $ecInt = new EcMath('1', $G, $math);
        $this->assertInstanceOf('Mdanter\Ecc\EcMath', $ecInt);
        $this->assertSame('1', $ecInt->result());

        $point = new Point($G->getCurve(), '73860570345112489656772034832846662006004986975604346631559066988788718814653', '41411225685712237035336738056202424213651816215153045928424574041669488255541', $G->getOrder(), $math);
        $ecPoint = new EcMath($point, $G, $math);
        $this->assertInstanceOf('Mdanter\Ecc\EcMath', $ecPoint);
        $this->assertSame($point, $ecPoint->result());
    }

    /**
     * @dataProvider getAdapters
     * @expectedException \LogicException
     * @expectedExceptionMessage Must provide a point or integer
     */
    public function testCreateFails(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $fail = new EcMath('string', $G, $math);
    }

    /**
     * @dataProvider getAdapters
     */
    public function testToPoint(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $ecInt = new EcMath(2, $G, $math);
        $ecInt->toPoint();
        $p = $ecInt->result();
        $this->assertInstanceOf('Mdanter\Ecc\PointInterface', $p);
        $this->assertEquals('89565891926547004231252920425935692360644145829622209833684329913297188986597', $p->getX());
        $this->assertEquals('12158399299693830322967808612713398636155367887041628176798871954788371653930', $p->getY());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testToPointAlreadyAPoint(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $ecInt = new EcMath(2, $G, $math);

        $ec = new EcMath($ecInt->getPoint(), $G, $math);
        $this->assertEquals($ec->toPoint(), $ecInt->toPoint());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testAddIntAndInt(MathAdapter $math)
    {
        $G  = EccFactory::getSecgCurves($math)->generator256k1();

        $ec = new EcMath('2', $G, $math);
        $ec->add('2');

        $this->assertSame('4', $ec->result());

        $ec ->add(2)
            ->add(3);

        $this->assertSame('9', $ec->result());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testAddPointAndPoint(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $p2= new EcMath('2', $G, $math);
        $p2= $p2->getPoint();

        $p = new EcMath('2', $G, $math);
        $p = $p
            ->toPoint()
            ->add($p2)
            ->result();

        $this->assertInstanceOf('Mdanter\Ecc\PointInterface', $p);
        $this->assertEquals('103388573995635080359749164254216598308788835304023601477803095234286494993683', $p->getX());
        $this->assertEquals('37057141145242123013015316630864329550140216928701153669873286428255828810018', $p->getY());

    }

    /**
     * @dataProvider getAdapters
     */
    public function testAddIntAndPoint(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $p2 = new EcMath('2', $G, $math);
        $p2 = $p2->getPoint();

        $p = new EcMath('2', $G, $math);
        $p = $p->add($p2)
            ->result();

        $this->assertInstanceOf('Mdanter\Ecc\PointInterface', $p);
        $this->assertEquals('103388573995635080359749164254216598308788835304023601477803095234286494993683', $p->getX());
        $this->assertEquals('37057141145242123013015316630864329550140216928701153669873286428255828810018', $p->getY());

    }

    /**
     * @dataProvider getAdapters
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot multiply two points together
     */
    public function testMulPointByPointFails(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $P = new Point($G->getCurve(), '73860570345112489656772034832846662006004986975604346631559066988788718814653', '41411225685712237035336738056202424213651816215153045928424574041669488255541', $G->getOrder(), $math);
        $fail = new EcMath($P, $G, $math);
        $fail->mul($P);
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMulIntByInt(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $x = new EcMath('2', $G, $math);
        $x = $x->mul('2')
            ->result();

        $this->assertEquals('4', $x);
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMulIntByPoint(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $p = new EcMath('2', $G, $math);
        $p->mul($G);

        $this->assertInstanceOf('Mdanter\Ecc\PointInterface', $p->result());
        $this->assertEquals('89565891926547004231252920425935692360644145829622209833684329913297188986597', $p->result()->getX());
        $this->assertEquals('12158399299693830322967808612713398636155367887041628176798871954788371653930', $p->result()->getY());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMod(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $m1 = new EcMath('2', $G, $math);
        $m1 = $m1
            ->mod($G->getOrder())
            ->result();
        $this->assertSame('2', $m1);

        $m2 = new EcMath('2', $G, $math);
        $m2 = $m2
            ->add($G->getOrder())
            ->mod($G->getOrder())
            ->result();
        $this->assertSame('2', $m2);
    }

    /**
     * @dataProvider getAdapters
     * @expectedException \LogicException
     * @expectedExceptionMessage Parameter for mod() must be an integer
     */
    public function testModFail(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $P = new Point($G->getCurve(), '73860570345112489656772034832846662006004986975604346631559066988788718814653', '41411225685712237035336738056202424213651816215153045928424574041669488255541', $G->getOrder(), $math);

        $e = new EcMath('2', $G, $math);
        $e->mod($P);

    }

    /**
     * @dataProvider getAdapters
     */
    public function testAddEcAndIntEquivalent(MathAdapter $math)
    {
        $G  = EccFactory::getSecgCurves($math)->generator256k1();

        $privKey = new EcMath('2', $G, $math);
        $pubKey  = $privKey->result();

        // (k+k)*G
        $ec1 = new EcMath('2', $G, $math);
        $ec1->add('2')
            ->mul($G);
        $result = $ec1->getPoint();

        // (k*G)+k
        $ec2 = new EcMath('2', $G, $math);
        $ec2->mul($G)
            ->add('2');

        // (k*G)+(k*G)
        $ec3 = new EcMath('2', $G, $math);
        $ec3->mul($G)
            ->add($pubKey);

        $this->assertEquals($ec1->result(), $result);
        $this->assertEquals($ec2->result(), $result);
        $this->assertEquals($ec3->result(), $result);

    }

    /**
     * @dataProvider getAdapters
     */
    public function testMulEcAndIntEquivalent(MathAdapter $math)
    {
        $G  = EccFactory::getSecgCurves($math)->generator256k1();
        $secret = '2';

        // (k*G)*2
        $ec = (new EcMath($secret, $G, $math))
            ->mul($G)
            ->mul(2);

        // (k*2)*G
        $ec2 = (new EcMath($secret, $G, $math))
            ->mul(2)
            ->mul($G);

        $this->assertEquals($ec->result(), $ec2->result());

    }

    /**
     * @dataProvider getAdapters
     */
    public function testGetDoubleEquivalent(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        // dbl(k) * G  == dbl(k*G)

        $int = new EcMath('2', $G, $math);
        $int = $int
            ->getDouble()
            ->toPoint();

        $p   = new EcMath('2', $G, $math);
        $p   = $p->toPoint()
            ->getDouble();

        $this->assertInstanceOf('Mdanter\Ecc\PointInterface', $p->result());
        $this->assertEquals('103388573995635080359749164254216598308788835304023601477803095234286494993683', $p->result()->getX());
        $this->assertEquals('37057141145242123013015316630864329550140216928701153669873286428255828810018', $p->result()->getY());

        $this->assertEquals($int->result(), $p->result());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testGetType(MathAdapter $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $ec = new EcMath('2', $G, $math);
        $this->assertSame('int', $ec->getType());
        $ec->toPoint();
        $this->assertSame('point', $ec->getType());

    }

    /**
     * @dataProvider getAdapters
     */
    public function testCmp(MathAdapter $math)
    {
        $G  = EccFactory::getSecgCurves($math)->generator256k1();
        $ec = new EcMath('2', $G, $math);

        $this->assertTrue($ec->cmp('3') == -1);
        $this->assertTrue($ec->cmp('2') == 0);
        $this->assertTrue($ec->cmp('1') == 1);

        $ec = new EcMath('2', $G, $math);
        $ec->toPoint();
        $this->assertTrue($ec->cmp($ec->getPoint()) == 0);

        $ec1 = new EcMath('3', $G, $math);
        $ec1->toPoint();
        $this->assertTrue($ec->cmp($ec1->getPoint()) == 1);
    }

    /**
     * @dataProvider getAdapters
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot compare values of different types
     */
    public function testCmpDifferentTypes(MathAdapter $math)
    {
        $G  = EccFactory::getSecgCurves($math)->generator256k1();
        $ec = new EcMath('2', $G, $math);
        $ec ->toPoint();
        $ec->cmp('2');
    }

    /**
     * @dataProvider getAdapters
     */
    public function testSimpleDeterministicAlgorithm(MathAdapter $math)
    {

        // Set $offset to '0' to confirm it matches with the first output of this program
        $G  = EccFactory::getSecgCurves($math)->generator256k1();

        $secret = '2';
        $sharedOffsetDerivedFromMasterPubkey = '2';
        $pubkey = (new EcMath($secret, $G, $math))->getPoint();

        // (P+o)%n  -> Only has point
        $pubData = (new EcMath($pubkey, $G, $math))
            ->add($sharedOffsetDerivedFromMasterPubkey)
            ->mod($G->getOrder());
        $this->assertSame('point', $pubData->getType());
        $this->assertSame('103388573995635080359749164254216598308788835304023601477803095234286494993683', $pubData->result()->getX());
        $this->assertSame('37057141145242123013015316630864329550140216928701153669873286428255828810018', $pubData->result()->getY());

        // (k+o)%n  -> Result is int, for the same point.
        $prvData = (new EcMath($secret, $G, $math))
            ->add($sharedOffsetDerivedFromMasterPubkey)
            ->mod($G->getOrder());
        $this->assertSame('int', $prvData->getType());
        $this->assertSame('4', $prvData->result());

        $pub = $prvData->getPoint();
        $this->assertSame('103388573995635080359749164254216598308788835304023601477803095234286494993683', $pub->getX());
        $this->assertSame('37057141145242123013015316630864329550140216928701153669873286428255828810018', $pub->getY());
    }
}