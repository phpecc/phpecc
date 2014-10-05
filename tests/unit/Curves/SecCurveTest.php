<?php

namespace Mdanter\Ecc\Tests\Curves;

use Mdanter\Ecc\MathAdapter;
use Mdanter\Ecc\Math\Gmp;
use Mdanter\Ecc\Math\BcMath;
use Mdanter\Ecc\Tests\AbstractTestCase;
use Mdanter\Ecc\EccFactory;

class SecgCurveTest extends AbstractTestCase
{

    public function getCurveParams()
    {
        return $this->_getAdapters([
            [ 'curve256k1', '0', '7', '115792089237316195423570985008687907853269984665640564039457584007908834671663' ],
            [ 'curve256r1', '115792089210356248762697446949407573530086143415290314195533631308867097853948', '41058363725152142129326129780047268409114441015993725554835256314039467401291', '115792089210356248762697446949407573530086143415290314195533631308867097853951' ],
            [ 'curve384r1', '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112316', '27580193559959705877849011840389048093056905856361568521428707301988689241309860865136260764883745107765439761230575', '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112319' ]
        ]);
    }

    /**
     *
     * @dataProvider getCurveParams
     */
    public function testCurveGeneration(MathAdapter $math, $function, $a, $b, $prime)
    {
        $factory = EccFactory::getSecgCurves($math);
        $curve = $factory->{$function}();

        $this->assertInstanceOf('\Mdanter\Ecc\CurveFpInterface', $curve);
        $this->assertEquals($a, $curve->getA());
        $this->assertEquals($b, $curve->getB());
        $this->assertEquals($prime, $curve->getPrime());
    }

    public function getGeneratorParams()
    {
        return $this->_getAdapters([
            [ 'generator256k1', '115792089237316195423570985008687907852837564279074904382605163141518161494337', '115792089237316195423570985008687907853269984665640564039457584007908834671663' ],
            [ 'generator256r1', '115792089210356248762697446949407573529996955224135760342422259061068512044369', '115792089210356248762697446949407573530086143415290314195533631308867097853951' ],
            [ 'generator384r1', '39402006196394479212279040100143613805079739270465446667946905279627659399113263569398956308152294913554433653942643', '39402006196394479212279040100143613805079739270465446667948293404245721771496870329047266088258938001861606973112319' ]
        ]);
    }

    /**
     *
     * @dataProvider getGeneratorParams
     */
    public function testGeneratorGeneration(MathAdapter $math, $function, $order, $prime)
    {
        $factory = EccFactory::getSecgCurves($math);
        $generator = $factory->{$function}();

        $this->assertInstanceOf('\Mdanter\Ecc\PointInterface', $generator);
        $this->assertEquals($order, $generator->getOrder());
        $this->assertEquals($prime, $generator->getCurve()->getPrime());
    }
}
