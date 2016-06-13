<?php

namespace Mdanter\Ecc\Serializer\Point;


use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Primitives\PointInterface;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;

class CompressedPointSerializer
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @var \Mdanter\Ecc\Math\NumberTheory
     */
    private $theory;

    /**
     * CompressedPointSerializer constructor.
     * @param GmpMathInterface $adapter
     */
    public function __construct(GmpMathInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->theory = $adapter->getNumberTheory();
    }

    /**
     * @param PointInterface $point
     * @return string
     */
    public function serialize(PointInterface $point)
    {
        $math = $this->adapter;
        $prefix = $math->equals($math->mod($point->getY(), gmp_init(2, 10)), gmp_init(0)) ? '02' : '03';
        $length = CurveOidMapper::getByteSize($point->getCurve()) * 2;

        $hexString = $prefix;
        $hexString .= str_pad(gmp_strval($point->getX(), 16), $length, '0', STR_PAD_LEFT);

        return $hexString;
    }

    /**
     * @param CurveFpInterface $curve
     * @param string $data - hex serialized compressed point
     * @return PointInterface
     */
    public function unserialize(CurveFpInterface $curve, $data)
    {
        $prefix = substr($data, 0, 2);

        $x = gmp_init(substr($data, 2), 16);
        $y = $this->theory->recoverYfromX($curve, $prefix === '03', $x);

        return $curve->getPoint($x, $y);
    }
}