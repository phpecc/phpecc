<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Util\NumberSize;

class HmacRandomNumberGenerator implements RandomNumberGeneratorInterface
{
    /**
     * @var MathAdapterInterface
     */
    private $math;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * @var PrivateKeyInterface
     */
    private $privateKey;

    /**
     * @var int
     */
    private $messageHash;

    /**
     * @var array
     */
    private $algSize = array(
        'sha1' => 160,
        'sha224' => 224,
        'sha256' => 256,
        'sha384' => 385,
        'sha512' => 512
    );

    /**
     * Hmac constructor.
     * @param MathAdapterInterface $math
     * @param PrivateKeyInterface $privateKey
     * @param int $messageHash - decimal hash of the message (*may* be truncated)
     * @param string $algorithm - hashing algorithm
     */
    public function __construct(MathAdapterInterface $math, PrivateKeyInterface $privateKey, $messageHash, $algorithm)
    {
        if (!isset($this->algSize[$algorithm])) {
            throw new \InvalidArgumentException('Unsupported hashing algorithm');
        }

        $this->math = $math;
        $this->algorithm = $algorithm;
        $this->privateKey = $privateKey;
        $this->messageHash = $messageHash;
    }

    /**
     * @param string $bits - binary string of bits
     * @param int $qlen - length of q in bits
     * @return int|string
     */
    public function bits2int($bits, $qlen)
    {
        $vlen = strlen($bits) * 8;
        $hex = bin2hex($bits);
        $hex = strlen($hex) % 2 == 0 ? $hex : '0' . $hex;
        $v = $this->math->baseConvert($hex, 16, 10);

        if ($vlen > $qlen) {
            $v = $this->math->rightShift($v, ($vlen - $qlen));
        }

        return $v;
    }

    /**
     * @param string $bits - a byte string
     * @param $q - generator order
     * @param $qlen - length of q in bits
     * @param $rlen - rounded octet length
     * @return string
     */
    public function bits2octets($bits, $q, $qlen, $rlen)
    {
        $z1 = $this->bits2int($bits, $qlen);
        $z2 = $this->math->sub($z1, $q);
        if ($this->math->cmp($z2, 0) < 0) {
            return $this->int2octets($z1, $rlen);
        }

        return $this->int2octets($z2, $rlen);
    }

    /**
     * @param int $int
     * @param int $rlen - rounded octet length
     * @return string
     */
    public function int2octets($int, $rlen)
    {
        $out = pack("H*", $this->math->decHex($int));
        if (strlen($out) < $rlen) {
            return str_pad('', $rlen - strlen($out), "\x00") . $out;
        }

        if (strlen($out) > $rlen) {
            return substr($out, 0, $rlen);
        }

        return $out;
    }

    /**
     * @param string $algorithm
     * @return int
     */
    private function getHashLength($algorithm)
    {
        return $this->algSize[$algorithm];
    }

    /**
     * @param $q
     * @return int|string
     */
    public function generate($q)
    {
        $qlen = NumberSize::bnNumBits($this->math, $q);
        $rlen = $this->math->rightShift($this->math->add($qlen, 7), 3);
        $hlen = $this->getHashLength($this->algorithm);
        $bx = $this->int2octets($this->privateKey->getSecret(), $rlen) . $this->int2octets($this->messageHash, $rlen);

        $v = str_pad('', $hlen / 8, "\x01", STR_PAD_LEFT);
        $k = str_pad('', $hlen / 8, "\x00", STR_PAD_LEFT);

        $k = hash_hmac($this->algorithm, $v . "\x00" . $bx, $k, true);
        $v = hash_hmac($this->algorithm, $v, $k, true);

        $k = hash_hmac($this->algorithm, $v . "\x01" . $bx, $k, true);
        $v = hash_hmac($this->algorithm, $v, $k, true);

        $t = '';
        for (;;) {
            $toff = 0;
            while ($toff < $rlen) {
                $v = hash_hmac($this->algorithm, $v, $k, true);
                $cc = min(strlen($v), $rlen - $toff);
                $t .= substr($v, 0, $cc);
                $toff += $cc;
            }

            $k = $this->bits2int($t, $qlen);
            if ($this->math->cmp($k, 0) > 0 && $this->math->cmp($k, $q) < 0) {
                return $k;
            }

            $k = hash_hmac($this->algorithm, $v . "\x00", $k, true);
            $v = hash_hmac($this->algorithm, $v, $k, true);
        }
    }
}
