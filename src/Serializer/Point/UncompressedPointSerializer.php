<?php

namespace Mdanter\Ecc\Serializer\Point;

use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Primitives\PointInterface;
use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;

class UncompressedPointSerializer implements PointSerializerInterface
{
    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @param GmpMathInterface     $adapter
     * @param bool                 $debug
     */
    public function __construct(GmpMathInterface $adapter, $debug = false)
    {
        $this->adapter = $adapter;
        $this->debug = (bool) $debug;
    }

    /**
     * @param PointInterface $point
     * @return string
     */
    public function serialize(PointInterface $point)
    {
        $length = CurveOidMapper::getByteSize($point->getCurve()) * 2;

        if ($this->debug) {
            error_log('Detected length: '.$length);
            error_log('Unpadded:'.$this->adapter->decHex($point->getX()));
            error_log('Unpadded len:'.strlen($this->adapter->decHex($point->getX())));
            error_log('Padded: '.str_pad($this->adapter->decHex($point->getX()), $length, '0', STR_PAD_LEFT));
        }

        $hexString = '04';
        $hexString .= str_pad(gmp_strval($point->getX(), 16), $length, '0', STR_PAD_LEFT);
        $hexString .= str_pad(gmp_strval($point->getY(), 16), $length, '0', STR_PAD_LEFT);

        if ($this->debug) {
            error_log('Resulting length: '.strlen($hexString));
            error_log('Hex: '.$hexString);
        }

        return $hexString;
    }

    /**
     * @param CurveFpInterface $curve
     * @param string           $data
     * @return PointInterface
     */
    public function unserialize(CurveFpInterface $curve, $data)
    {
        if (substr($data, 0, 2) != '04') {
            throw new \InvalidArgumentException('Invalid data: only uncompressed keys are supported.');
        }

        $data = substr($data, 2);
        $dataLength = strlen($data);

        $x = gmp_init(substr($data, 0, $dataLength / 2), 16);
        $y = gmp_init(substr($data, $dataLength / 2), 16);

        return $curve->getPoint($x, $y);
    }
}
