<?php

namespace Mdanter\Ecc\Tests\Math;

use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Math\EcMath;
use Mdanter\Ecc\Primitives\Point;
use Mdanter\Ecc\Tests\AbstractTestCase;

class EcMathTest extends AbstractTestCase
{

    /**
     *
     * @dataProvider getAdapters
     * @testdox Test point additions yield expected results
     */
    public function testCreateNew(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $ecInt = $math->getEcMath($G, 1);
        $this->assertInstanceOf($this->classEcMath, $ecInt);
        $this->assertSame(1, $ecInt->result());

        $ecInt = $math->getEcMath($G, '1');
        $this->assertInstanceOf($this->classEcMath, $ecInt);
        $this->assertSame('1', $ecInt->result());

        $point = new Point($math, $G->getCurve(), '73860570345112489656772034832846662006004986975604346631559066988788718814653', '41411225685712237035336738056202424213651816215153045928424574041669488255541', $G->getOrder());
        $ecPoint = $math->getEcMath($G, $point);
        $this->assertInstanceOf($this->classEcMath, $ecPoint);
        $this->assertSame($point, $ecPoint->result());
    }

    /**
     * @dataProvider getAdapters
     * @expectedException \LogicException
     * @expectedExceptionMessage Must provide a point or integer
     */
    public function testCreateFails(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $math->getEcMath($G, 'string');
    }

    /**
     * @dataProvider getAdapters
     */
    public function testToPoint(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $ecInt = $math->getEcMath($G, 2);
        $ecInt->toPoint();
        $p = $ecInt->result();
        $this->assertInstanceOf($this->classPointInterface, $p);
        $this->assertEquals('89565891926547004231252920425935692360644145829622209833684329913297188986597', $p->getX());
        $this->assertEquals('12158399299693830322967808612713398636155367887041628176798871954788371653930', $p->getY());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testToPointAlreadyAPoint(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $ecInt = $math->getEcMath($G, 2);
        $ec = $math->getEcMath($G, $ecInt->getPoint());

        $this->assertEquals($ec->toPoint(), $ecInt->toPoint());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testAddIntAndInt(MathAdapterInterface $math)
    {
        $G  = EccFactory::getSecgCurves($math)->generator256k1();

        $ec = $math->getEcMath($G, '2')->add('2');
        $this->assertSame('4', $ec->result());

        $ec->add(2)->add(3);
        $this->assertSame('9', $ec->result());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testAddPointAndPoint(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $p2 = $math->getEcMath($G, '2')
            ->getPoint();

        $p = $math->getEcMath($G, '2')
            ->toPoint()
            ->add($p2)
            ->result();

        $this->assertInstanceOf($this->classPointInterface, $p);
        $this->assertEquals('103388573995635080359749164254216598308788835304023601477803095234286494993683', $p->getX());
        $this->assertEquals('37057141145242123013015316630864329550140216928701153669873286428255828810018', $p->getY());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testAddIntAndPoint(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $p2 = $math->getEcMath($G, '2')->getPoint();
        $p = $math->getEcMath($G, '2')->add($p2)->result();

        $this->assertInstanceOf($this->classPointInterface, $p);
        $this->assertEquals('103388573995635080359749164254216598308788835304023601477803095234286494993683', $p->getX());
        $this->assertEquals('37057141145242123013015316630864329550140216928701153669873286428255828810018', $p->getY());
    }

    /**
     * @dataProvider getAdapters
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot multiply two points together
     */
    public function testMulPointByPointFails(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $P = new Point($math, $G->getCurve(), '73860570345112489656772034832846662006004986975604346631559066988788718814653', '41411225685712237035336738056202424213651816215153045928424574041669488255541', $G->getOrder());
        $fail = $math->getEcMath($G, $P)->mul($P);
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMulIntByInt(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $x = $math->getEcMath($G, '2')
            ->mul('2')
            ->result();

        $this->assertEquals('4', $x);
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMulIntByPoint(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $p = $math->getEcMath($G, '2')->mul($G);

        $this->assertInstanceOf($this->classPointInterface, $p->result());
        $this->assertEquals('89565891926547004231252920425935692360644145829622209833684329913297188986597', $p->result()->getX());
        $this->assertEquals('12158399299693830322967808612713398636155367887041628176798871954788371653930', $p->result()->getY());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMod(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        $m1 = $math->getEcMath($G, '2')
            ->result();
        $this->assertSame('2', $m1);

        $m2 = $math->getEcMath($G, '2')
            ->add($G->getOrder())
            ->result();
        $this->assertSame('2', $m2);
    }

    /**
     * @dataProvider getAdapters
     */
    public function testAddEcAndIntEquivalent(MathAdapterInterface $math)
    {
        $G  = EccFactory::getSecgCurves($math)->generator256k1();

        $secret = '2';
        $privKey = $math->getEcMath($G, $secret);
        $pubKey  = $math->getEcMath($G, $secret)->toPoint();

        // (k+k)*G
        $ec1 = $math->getEcMath($G, $privKey->result())
            ->add($privKey->result())
            ->mul($G);

        $detPoint = $ec1->getPoint();

        // (k*G)+k
        $ec2 = $math->getEcMath($G, $privKey->result())
            ->mul($G)
            ->add($privKey->result());

        // (k*G)+(k*G)
        $ec3 = $math->getEcMath($G, $privKey->result())
            ->mul($G)
            ->add($pubKey->result());

        $this->assertTrue($ec1->result()->equals($detPoint));
        $this->assertTrue($ec2->result()->equals($detPoint));
        $this->assertTrue($ec3->result()->equals($detPoint));
    }

    /**
     * @dataProvider getAdapters
     */
    public function testMulEcAndIntEquivalent(MathAdapterInterface $math)
    {
        $G  = EccFactory::getSecgCurves($math)->generator256k1();
        $secret = '2';

        // (k*G)*2
        $ec = $math->getEcMath($G, $secret)
            ->mul($G)
            ->mul(2);

        // (k*2)*G
        $ec2 = $math->getEcMath($G, $secret)
            ->mul(2)
            ->mul($G);

        $this->assertTrue($ec->result()->equals($ec2->result()));
    }

    /**
     * @dataProvider getAdapters
     */
    public function testGetDoubleEquivalent(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();

        // dbl(k) * G  == dbl(k*G)

        $int = $math->getEcMath($G, '2')->getDouble()->toPoint();
        $p = $math->getEcMath($G, '2')->toPoint()->getDouble();

        $this->assertInstanceOf($this->classPointInterface, $p->result());
        $this->assertEquals('103388573995635080359749164254216598308788835304023601477803095234286494993683', $p->result()->getX());
        $this->assertEquals('37057141145242123013015316630864329550140216928701153669873286428255828810018', $p->result()->getY());

        $this->assertTrue($int->result()->equals($p->result()));
    }

    /**
     * @dataProvider getAdapters
     */
    public function testGetType(MathAdapterInterface $math)
    {
        $G = EccFactory::getSecgCurves($math)->generator256k1();
        $secret = '2';

        $ec = $math->getEcMath($G, $secret);
        $this->assertSame('int', $ec->getType());

        $ec->toPoint();
        $this->assertSame('point', $ec->getType());
    }

    /**
     * @dataProvider getAdapters
     */
    public function testCmp(MathAdapterInterface $math)
    {
        $G  = EccFactory::getSecgCurves($math)->generator256k1();
        $secret = '2';
        $ec = $math->getEcMath($G, $secret);

        $this->assertTrue($ec->cmp('3') == -1);
        $this->assertTrue($ec->cmp('2') == 0);
        $this->assertTrue($ec->cmp('1') == 1);

        $ec = $math->getEcMath($G, $secret)->toPoint();
        $this->assertTrue($ec->cmp($ec->getPoint()) == 0);

        $ec1 = $math->getEcMath($G, '3')->toPoint();
        $this->assertTrue($ec->cmp($ec1->getPoint()) == 1);
    }

    /**
     * @dataProvider getAdapters
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot compare values of different types
     */
    public function testCmpDifferentTypes(MathAdapterInterface $math)
    {
        $G  = EccFactory::getSecgCurves($math)->generator256k1();
        $math->getEcMath($G, '2')->toPoint()->cmp('2');
    }

    /**
     * @dataProvider getAdapters
     */
    public function testSimpleDeterministicAlgorithm(MathAdapterInterface $math)
    {

        // Set $offset to '0' to confirm it matches with the first output of this program
        $G  = EccFactory::getSecgCurves($math)->generator256k1();

        $secret = '2';
        $sharedOffsetDerivedFromMasterPubkey = '2';
        $pubkey = $math->getEcMath($G, $secret)->getPoint();

        // (P+o)%n  -> Only has point
        $pubData = $math->getEcMath($G, $pubkey)
            ->add($sharedOffsetDerivedFromMasterPubkey);
        $this->assertSame('point', $pubData->getType());
        $this->assertSame('103388573995635080359749164254216598308788835304023601477803095234286494993683', $pubData->result()->getX());
        $this->assertSame('37057141145242123013015316630864329550140216928701153669873286428255828810018', $pubData->result()->getY());

        // (k+o)%n  -> Result is int, for the same point.
        $prvData = $math->getEcMath($G, $secret)
            ->add($sharedOffsetDerivedFromMasterPubkey);
        $this->assertSame('int', $prvData->getType());
        $this->assertSame('4', $prvData->result());

        $pub = $prvData->getPoint();
        $this->assertSame('103388573995635080359749164254216598308788835304023601477803095234286494993683', $pub->getX());
        $this->assertSame('37057141145242123013015316630864329550140216928701153669873286428255828810018', $pub->getY());
    }
}
