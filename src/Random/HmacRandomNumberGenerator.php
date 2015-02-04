<?php

namespace Mdanter\Ecc\Random;

use Mdanter\Ecc\MathAdapterInterface;
use Mdanter\Ecc\PrivateKeyInterface;
use Mdanter\Ecc\RandomNumberGeneratorInterface;
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
     * @param MathAdapterInterface $math
     * @param PrivateKeyInterface  $privateKey
     * @param string               $messageHash
     * @param $algo
     * @internal param string $personalString
     */
    public function __construct(MathAdapterInterface $math, PrivateKeyInterface $privateKey, $messageHash, $algo)
    {
        if (!in_array($algo, hash_algos())) {
            throw new \RuntimeException('HMACDRGB: Hashing algorithm not found');
        }

        $this->math = $math;
        $this->algorithm = $algo;

        $hlen = strlen(hash($algo, 1, true));
        $vlen = 8 * ceil($hlen / 8);

        // Initialize deterministic vectors
        $this->V = str_pad('', $vlen, chr(0x01), STR_PAD_LEFT);
        $this->K = str_pad('', $vlen, chr(0x00), STR_PAD_LEFT);

        // Encode the private key and hash as binary, a seed for the DRBG
        $hex     = str_pad($math->decHex($privateKey->getSecret()), $hlen * 2, '0', STR_PAD_LEFT);
        $hash    = str_pad($math->decHex($messageHash), $hlen * 2, '0', STR_PAD_LEFT);
        $entropy = pack("H*", $hex.$hash);

        $this->update($entropy);

        return $this;
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
        $this->K = $this->hash(sprintf(
            "%s%s%s",
            $this->V,
            chr(0x00),
            $data
        ));

        $this->V = $this->hash($this->V);

        if ($data) {
            $this->K = $this->hash(sprintf(
                "%s%s%s",
                $this->V,
                chr(0x01),
                $data
            ));

            $this->V = $this->hash($this->V);
        }

        return $this;
    }

    /**
     * Load $numBytes bytes from the DRBG
     *
     * @param  int    $numNumBytes
     * @return string
     */
    private function bytes($numNumBytes)
    {
        $temp = "";

        // Build a string of $numBytes bytes from hashing the seeded DRBG
        while (strlen($temp) < $numNumBytes) {
            $this->V = $this->hash($this->V);
            $temp   .= $this->V;
        }

        $this->update(null);
        $this->reseedCounter++;

        return substr($temp, 0, $numNumBytes);
    }

    /**
     * Generate a nonce based on the given $max
     *
     * @param $max
     * @return int|string
     */
    public function generate($max)
    {
        if (is_null($this->result)) {
            $v     = NumberSize::getCeiledByteSize($this->math, $max);

            while (true) {
                $bytes = $this->bytes($v);
                $hex   = bin2hex($bytes);
                $rand  = $this->math->hexDec($hex);

                // Check k is between [1, ... $max]
                if ($this->math->cmp(1, $rand) <= 0 && $this->math->cmp($rand, $max) < 0) {
                    break;
                }

                // Otherwise derive another and try again.
                $this->update(null);
            }

            $this->result = $rand;
        }

        return $this->result;
    }
}
