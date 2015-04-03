<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Util\NumberSize;

/**
 * Class HMACRandomNumberGenerator
 *
 * RFC6979 https://tools.ietf.org/html/rfc6979
 * Instance saves result for later accesses once called,
 * as only one valid nonce should come from a key + message hash pair.
 *
 * Could be separated out for a fully fledged HMACDRBG in future,
 * which would require locking when reseed counter exceeds 10,000,
 * until it is reseeded again.
 */
class HmacRandomNumberGenerator implements RandomNumberGeneratorInterface
{
    /**
     * @var string
     */
    private $algorithm;

    /**
     * @var string
     */
    private $K;

    /**
     * @var string
     */
    private $V;

    /**
     * @var int
     */
    private $reseedCounter;

    /**
     * @var
     */
    private $math;

    /**
     * @var
     */
    private $result;

    /**
     * Construct a HMAC deterministic byte generator.
     *
     * @param    MathAdapterInterface $math
     * @param    PrivateKeyInterface  $privateKey
     * @param    string               $messageHash
     * @param    $algo
     * @internal param string $personalString
     */
    public function __construct(MathAdapterInterface $math, PrivateKeyInterface $privateKey, $messageHash, $algo)
    {
        if (!in_array($algo, hash_algos())) {
            throw new \RuntimeException('HMACDRGB: Hashing algorithm not found');
        }

        $tempHash = hash($algo, 1, true);
        $vlen = NumberSize::getCeiledByteSize($math, $math->hexdec(bin2hex($tempHash), 1));

        // Initialize deterministic vectors
        $this->V = str_pad('', $vlen, chr(0x01), STR_PAD_LEFT);
        $this->K = str_pad('', $vlen, chr(0x00), STR_PAD_LEFT);

        $this->math = $math;
        $this->algorithm = $algo;
        $this->generator = $privateKey->getPoint();

        // Encode the private key and hash as binary, a seed for the DRBG
        $entropy = pack(
            "H*",
            $this->int2octets($privateKey->getSecret()).
            $this->int2octets($messageHash)
        );

        $this->update($entropy);

        return $this;
    }
    private function bitLength($number)
    {
        return NumberSize::bnNumBits($this->math, $number);
    }

    /**
     * @return number
     */
    public function qBitLen()
    {
        return $this->bitLength($this->generator->getOrder());
    }

    /**
     * @return int
     */
    public function hashBitLength()
    {
        return strlen(hash($this->algorithm, 1, true)) * 8;
    }

    /**
     * @return mixed
     */
    public function rolen()
    {
        return $this->math->rightShift($this->math->add($this->qBitLen(), 7), 3);
    }

    /**
     * @param $data
     * @return int|string
     */
    public function bits2int($data)
    {
        $vlen = strlen($data) * 8;
        $v    = $this->math->stringToInt($data);

        if ($vlen > $this->qBitLen()) {
            //echo ">1\n";
            $v = $this->math->rightShift($v, ($vlen - $this->qBitLen()));
        }
        return $v;
    }

    /**
     * @param $v
     * @return int|string
     */
    public function int2octets($v)
    {
        $out = $this->math->decHex($v);
        $vlen = strlen($out);
        //echo "V: $vlen, R:".$this->rolen()."\n";

        if ($vlen < $this->rolen() * 2) {
            //echo " >2 \n";
            $out = str_pad($out, $this->rolen() * 2, '0', STR_PAD_LEFT);
        }

        if ($vlen > $this->rolen() * 2) {
            //echo "<3\n";
            $out = substr($out, 0, $this->rolen() * 2);
        }
        //echo "out: $out\n";
        return $out;
    }

    /**
     * @param $in
     * @return int|string
     */
    public function bits2octets($in)
    {
        $z1 = $this->bits2int($in);
        $z2 = $this->math->sub($z1, $this->generator->getOrder());
        if ($this->math->cmp($z2, 0) < 0) {
            return $this->int2octets($z1);
        }
        return $this->int2octets($z2);
    }


    /**
     * Return the hash of the given binary $data
     * @param  string $data
     * @return string
     */
    private function hash($data)
    {
        $hash = hash_hmac($this->algorithm, $data, $this->K, true);
        return $hash;
    }

    /**
     * Update the K and V values.
     *
     * @param  null|string $data
     * @return $this
     */
    private function update($data = null)
    {
        $this->K = $this->hash(
            sprintf(
                "%s%s%s",
                $this->V,
                chr(0x00),
                $data
            )
        );

        $this->V = $this->hash($this->V);

        if ($data) {
            $this->K = $this->hash(
                sprintf(
                    "%s%s%s",
                    $this->V,
                    chr(0x01),
                    $data
                )
            );

            $this->V = $this->hash($this->V);
        }

        return $this;
    }

    /**
     * Load $numBytes bytes from the DRBG
     *
     * @param  int $numNumBytes
     * @return string
     */
    private function bytes($numNumBytes)
    {
        $temp = "";

        // Build a string of $numBytes bytes from hashing the seeded DRBG
        while (strlen($temp) < $numNumBytes) {
            $temp .= $this->V = $this->hash($this->V);
        }

        $this->update(null);
        $this->reseedCounter++;

        return substr($temp, 0, $numNumBytes);
    }

    /**
     * Generate a nonce based on the given $max
     *
     * {@inheritDoc}
     * @see \Mdanter\Ecc\Random\RandomNumberGeneratorInterface::generate()
     */
    public function generate($max)
    {
        if (is_null($this->result)) {
            $v = NumberSize::getCeiledByteSize($this->math, $max);

            while (true) {
                $hex  = bin2hex($this->bytes($v));
                $rand = $this->math->hexDec($hex);

                // Check k is between [1, ... $max]
                if ($this->math->cmp(1, $rand) <= 0 && $this->math->cmp($rand, $max) < 0) {
                    break;
                }

                // Otherwise derive another and try again.
                $this->update(chr(0));
            }

            $this->result = $rand;
        }

        return $this->result;
    }
}
