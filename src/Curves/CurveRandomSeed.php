<?php

namespace Mdanter\Ecc\Curves;


class CurveRandomSeed
{

    /**
     * @var array
     */
    private static $seedMap = [
        NistCurve::NAME_P192 => '3045AE6FC8422F64ED579528D38120EAE12196D5',
        NistCurve::NAME_P224 => 'BD71344799D5C7FCDC45B59FA3B9AB8F6A948BC5',
        NistCurve::NAME_P256 => 'C49D360886E704936A6678E1139D26B7819F7E90',
        NistCurve::NAME_P384 => 'A335926AA319A27A1D00896A6773A4827ACDAC73',
        NistCurve::NAME_P521 => 'D09E8800291CB85396CC6717393284AAA0DA64BA',
        SecgCurve::NAME_SECP_112R1 => '00F50B028E4D696E676875615175290472783FB1',
        SecgCurve::NAME_SECP_256R1 => 'C49D360886E704936A6678E1139D26B7819F7E90',
        SecgCurve::NAME_SECP_384R1 => 'A335926AA319A27A1D00896A6773A4827ACDAC73'
    ];

    /**
     * @param NamedCurveFp $curve
     */
    public static function getSeed(NamedCurveFp $curve)
    {
        $keys = array_keys(self::$seedMap);

        if (in_array($curve->getName(), $keys)) {
            $name = $curve->getName();
            $map = self::$seedMap;
            if (!isset($map[$name])) {
                throw new \RuntimeException('Curve not known to seed map');
            }

            return $map[$name];
        }

        throw new \InvalidArgumentException('Curve must be from NIST set');
    }
}