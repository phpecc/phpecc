<?php

namespace Mdanter\Ecc\Crypto\Routines\Signature;

use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Util\NumberSize;

class Signer
{

    /**
     *
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     *
     * @param MathAdapterInterface $adapter
     */
    public function __construct(MathAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param GeneratorPoint $G
     * @param $hash
     * @return int|string
     */
    public function truncateHash(GeneratorPoint $G, $hash)
    {
        $hexSize = strlen($this->adapter->decHex($hash));
        $hashBits = $this->adapter->baseConvert($hash, 10, 2);
        if (strlen($hashBits) < $hexSize * 4) {
            $hashBits = str_pad($hashBits, $hexSize * 4, '0', STR_PAD_LEFT);
        }

        $messageHash = $this->adapter->baseConvert(substr($hashBits, 0, NumberSize::bnNumBits($this->adapter, $G->getOrder())), 2, 10);
        return $messageHash;
    }

    /**
     * @param PrivateKeyInterface $key
     * @param $hash
     * @param $randomK
     * @return Signature
     */
    public function sign(PrivateKeyInterface $key, $hash, $randomK)
    {
        $math = $this->adapter;
        $generator = $key->getPoint();

        $n = $generator->getOrder();
        $k = $math->mod($randomK, $n);
        $p1 = $generator->mul($k);
        $r = $p1->getX();

        if ($math->cmp($r, 0) == 0) {
            throw new \RuntimeException("Error: random number R = 0");
        }

        $hash = $this->truncateHash($generator, $hash);

        $s = $math->mod(
            $math->mul(
                $math->inverseMod($k, $n),
                $math->mod($math->add($hash, $math->mul($key->getSecret(), $r)), $n)
            ),
            $n
        );

        if ($math->cmp($s, 0) == 0) {
            throw new \RuntimeException("Error: random number S = 0");
        }

        return new Signature($r, $s);
    }

    /**
     * @param PublicKeyInterface $key
     * @param SignatureInterface $signature
     * @param $hash
     * @return bool
     */
    public function verify(PublicKeyInterface $key, SignatureInterface $signature, $hash)
    {
        $math = $this->adapter;

        $generator = $key->getGenerator();
        $n = $generator->getOrder();
        $point = $key->getPoint();

        $r = $signature->getR();
        $s = $signature->getS();

        if ($math->cmp($r, 1) < 1 || $math->cmp($r, $math->sub($n, 1)) > 0) {
            return false;
        }

        if ($math->cmp($s, 1) < 1 || $math->cmp($s, $math->sub($n, 1)) > 0) {
            return false;
        }

        $c = $math->inverseMod($s, $n);
        $u1 = $math->mod($math->mul($hash, $c), $n);
        $u2 = $math->mod($math->mul($r, $c), $n);
        $xy = $generator->mul($u1)->add($point->mul($u2));
        $v = $math->mod($xy->getX(), $n);

        return $math->cmp($v, $r) == 0;
    }
}
