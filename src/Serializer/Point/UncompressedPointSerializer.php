<?php

namespace Mdanter\Ecc\Serializer\Point;

use Mdanter\Ecc\PointInterface;
use Mdanter\Ecc\CurveFpInterface;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\MathAdapterInterface;

class UncompressedPointSerializer implements PointSerializerInterface
{

    private $adapter;

    private $debug = false;

    public function __construct(MathAdapterInterface $adapter, $debug = false)
    {
        $this->adapter = $adapter;
        $this->debug = (bool) $debug;
    }

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
        $hexString .= str_pad($this->adapter->decHex($point->getX()), $length, '0', STR_PAD_LEFT);
        $hexString .= str_pad($this->adapter->decHex($point->getY()), $length, '0', STR_PAD_LEFT);

        if ($this->debug) {
            error_log('Resulting length: '.strlen($hexString));
            error_log('Hex: '.$hexString);
        }

        return $hexString;
    }

    public function unserialize(CurveFpInterface $curve, $data)
    {
        if (substr($data, 0, 2) != '04') {
            throw new \InvalidArgumentException('Invalid data: only uncompressed keys are supported.');
        }

        $data = substr($data, 2);
        $dataLength = strlen($data);

        $x = $this->adapter->hexDec(substr($data, 0, $dataLength / 2));
        $y = $this->adapter->hexDec(substr($data, $dataLength / 2));

        return $curve->getPoint($x, $y);
    }
}
