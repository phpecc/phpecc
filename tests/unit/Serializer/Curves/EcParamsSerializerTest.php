<?php

namespace Mdanter\Ecc\Tests\Serializer\Curves;


use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\Curves\EcParamsOidSerializer;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Tests\AbstractTestCase;

class EcParamsSerializerTest extends AbstractTestCase
{
    public function getVectors()
    {
        return [
            [
                'secp112r1',
                '-----BEGIN EC PARAMETERS-----
MIGLAgEBMBoGByqGSM49AQECDwDbfCq/YuNeZoB2vq0gizA3BA7bfCq/YuNeZoB2
vq0giAQOZZ74ugQ5Fu7eiRFwKyIDFQAA9QsCjk1pbmdodWFRdSkEcng/sQQdBAlI
cjmZWl7na1X5wvCYqJzlr4ckwKI+Dg/3dQACDwDbfCq/YuNedijfrGVhxQIBAQ==
-----END EC PARAMETERS-----'
            ],
            [
                'secp256k1',
                '-----BEGIN EC PARAMETERS-----
MIGiAgEBMCwGByqGSM49AQECIQD////////////////////////////////////+
///8LzAGBAEABAEHBEEEeb5mfvncu6xVoGKVzocLBwKb/NstzijZWfKBWxb4F5hI
Otp3JqPEZV2k+/wOEQio/Re0SKaFVBmcR9CP+xDUuAIhAP//////////////////
//66rtzmr0igO7/SXozQNkFBAgEB
-----END EC PARAMETERS-----'
            ],
            [
                'secp256r1',
                '-----BEGIN EC PARAMETERS-----
MIH3AgEBMCwGByqGSM49AQECIQD/////AAAAAQAAAAAAAAAAAAAAAP//////////
/////zBbBCD/////AAAAAQAAAAAAAAAAAAAAAP///////////////AQgWsY12Ko6
k+ez671VdpiGvGUdBrDMU7D2O848PifSYEsDFQDEnTYIhucEk2pmeOETnSa3gZ9+
kARBBGsX0fLhLEJH+Lzm5WOkQPJ3A32BLeszoPShOUXYmMKWT+NC4v4af5uO5+tK
fA+eFivOM1drMV7Oy7ZAaDe/UfUCIQD/////AAAAAP//////////vOb6racXnoTz
ucrC/GMlUQIBAQ==
-----END EC PARAMETERS-----'
            ],
            [
                'secp384r1',
                '-----BEGIN EC PARAMETERS-----
MIIBVwIBATA8BgcqhkjOPQEBAjEA////////////////////////////////////
//////7/////AAAAAAAAAAD/////MHsEMP//////////////////////////////
///////////+/////wAAAAAAAAAA/////AQwszEvp+I+5+SYjgVr4/gtGRgdnG7+
gUESAxQIj1ATh1rGVjmNii7RnSqFyO3T7CrvAxUAozWSaqMZonodAIlqZ3OkgnrN
rHMEYQSqh8oivosFN46xxx7zIK10bh07Younm5hZ90HgglQqOFUC8l2/VSlsOlRe
OHJ2Crc2F95KliYsb12emL+Sktwp+PQdvSiaFHzp2jETtfC4wApgsc4dfoGdekMd
fJDqDl8CMQD////////////////////////////////HY02B9Dct31gaDbJIsKd6
7OwZaszFKXMCAQE=
-----END EC PARAMETERS-----'
            ],
            [
                'nist-p192',
                '-----BEGIN EC PARAMETERS-----
MIHHAgEBMCQGByqGSM49AQECGQD////////////////////+//////////8wSwQY
/////////////////////v/////////8BBhkIQUZ5ZyA5w+n6atyJDBJ/rje7MFG
ubEDFQAwRa5vyEIvZO1XlSjTgSDq4SGW1QQxBBiNqA6wMJD2fL8g60OhiAD0/wr9
gv8QEgcZK5X/yNp4YxAR7WskzdVz+XehHnlIEQIZAP///////////////5ne+DYU
a8mxtNIoMQIBAQ==
-----END EC PARAMETERS-----'
            ],
            [
                'nist-p224',
                '-----BEGIN EC PARAMETERS-----
MIHfAgEBMCgGByqGSM49AQECHQD/////////////////////AAAAAAAAAAAAAAAB
MFMEHP////////////////////7///////////////4EHLQFCoUMBLOr9UEyVlBE
sLfXv9i6Jws5QyNV/7QDFQC9cTRHmdXH/NxFtZ+juauPapSLxQQ5BLcODL1rtL9/
MhOQuUoDwdNWwhEiNDKA1hFcHSG9N2OItfcj+0wi3+bNQ3WgWgdHZETVgZmFAH40
Ah0A//////////////////8WouC48D4T3SlFXFwqPQIBAQ==
-----END EC PARAMETERS-----'
            ],
            [
                'nist-p256',
                '-----BEGIN EC PARAMETERS-----
MIH3AgEBMCwGByqGSM49AQECIQD/////AAAAAQAAAAAAAAAAAAAAAP//////////
/////zBbBCD/////AAAAAQAAAAAAAAAAAAAAAP///////////////AQgWsY12Ko6
k+ez671VdpiGvGUdBrDMU7D2O848PifSYEsDFQDEnTYIhucEk2pmeOETnSa3gZ9+
kARBBGsX0fLhLEJH+Lzm5WOkQPJ3A32BLeszoPShOUXYmMKWT+NC4v4af5uO5+tK
fA+eFivOM1drMV7Oy7ZAaDe/UfUCIQD/////AAAAAP//////////vOb6racXnoTz
ucrC/GMlUQIBAQ==
-----END EC PARAMETERS-----'
            ],
            [
                'nist-p384',
                '-----BEGIN EC PARAMETERS-----
MIIBVwIBATA8BgcqhkjOPQEBAjEA////////////////////////////////////
//////7/////AAAAAAAAAAD/////MHsEMP//////////////////////////////
///////////+/////wAAAAAAAAAA/////AQwszEvp+I+5+SYjgVr4/gtGRgdnG7+
gUESAxQIj1ATh1rGVjmNii7RnSqFyO3T7CrvAxUAozWSaqMZonodAIlqZ3OkgnrN
rHMEYQSqh8oivosFN46xxx7zIK10bh07Younm5hZ90HgglQqOFUC8l2/VSlsOlRe
OHJ2Crc2F95KliYsb12emL+Sktwp+PQdvSiaFHzp2jETtfC4wApgsc4dfoGdekMd
fJDqDl8CMQD////////////////////////////////HY02B9Dct31gaDbJIsKd6
7OwZaszFKXMCAQE=
-----END EC PARAMETERS-----'
            ],
            [
                'nist-p521',
                '-----BEGIN EC PARAMETERS-----
MIIBwgIBATBNBgcqhkjOPQEBAkIB////////////////////////////////////
//////////////////////////////////////////////////8wgZ4EQgH/////
////////////////////////////////////////////////////////////////
/////////////////ARBUZU+uWGOHJofkpohoLaFQO6i2nJbmbMV87i0iZGO8Qnh
Vhk5Uex+k3sWUsC9O7G/BzVz34g9LDTx70Uf1GtQPwADFQDQnogAKRy4U5bMZxc5
MoSqoNpkugSBhQQAxoWOBrcEBOnNnj7LZiOVtEKcZIE5BT+1Ifgor2BrTT26oUte
d+/nWSj+HcEnov+o3jNIs8GFakKb+X5+McLlvWYBGDkpaniaO8AEXIpftCx9G9mY
9URJV5tEaBevvRcnPmYsl+5ymV70JkDFULkBP60HYTU8cIaicsJAiL6Udp/RZlAC
QgH///////////////////////////////////////////pRhoeDvy+Wa3/MAUj3
CaXQO7XJuImcR667b7cekThkCQIBAQ==
-----END EC PARAMETERS-----'
            ]
        ];
    }

    /**
     * @dataProvider getVectors
     */
    public function testAgainstOpenSSL($curveName, $expectedParams)
    {
        $math = EccFactory::getAdapter();
        $curve = CurveFactory::getCurveByName($curveName);
        $G = CurveFactory::getGeneratorByName($curveName);

        $pSer = new \Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer($math);
        $ser = new \Mdanter\Ecc\Serializer\Curves\EcParamsSerializer($pSer);

        $this->assertEquals($expectedParams, $ser->serialize($curve, $G));
    }
}