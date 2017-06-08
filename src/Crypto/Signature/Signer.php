<?php

namespace Mdanter\Ecc\Crypto\Signature;

use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Crypto\Key\PublicKeyInterface;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Util\NumberSize;
use Mdanter\Ecc\Util\BinaryString;

class Signer
{

    /**
     *
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     *
     * @param GmpMathInterface $adapter
     */
    public function __construct(GmpMathInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param GeneratorPoint $G
     * @param \GMP $hash
     * @return \GMP
     */
    public function truncateHash(GeneratorPoint $G, \GMP $hash)
    {
        $dec = $this->adapter->toString($hash);
        $hexSize = (int) ceil(BinaryString::length($this->adapter->decHex($dec)) / 4) * 4;

        $hashBits = $this->adapter->baseConvert($dec, 10, 2);
        if (BinaryString::length($hashBits) < $hexSize * 4) {
            $hashBits = str_pad($hashBits, $hexSize * 4, '0', STR_PAD_LEFT);
        }

        $messageHash = gmp_init(BinaryString::substring($hashBits, 0, NumberSize::bnNumBits($this->adapter, $G->getOrder())), 2);
        return $messageHash;
    }

    /**
     * @param GeneratorPoint $G
     * @param string $algorithm
     * @param string $data
     * @return \GMP
     */
    public function hashData(GeneratorPoint $G, $algorithm, $data)
    {
        if (!in_array($algorithm, hash_algos())) {
            throw new \InvalidArgumentException('Unsupported hashing algorithm');
        }

        $hash = gmp_init(hash($algorithm, $data, false), 16);
        return $this->truncateHash($G, $hash);
    }

    /**
     * @param PrivateKeyInterface $key
     * @param \GMP $hash
     * @param \GMP $randomK
     * @return Signature
     */
    public function sign(PrivateKeyInterface $key, \GMP $hash, \GMP $randomK)
    {
        $math = $this->adapter;
        $generator = $key->getPoint();
        $modMath = $math->getModularArithmetic($generator->getOrder());

        $hash = $this->truncateHash($generator, $hash);
        $k = $math->mod($randomK, $generator->getOrder());
        $p1 = $generator->mul($k);
        $r = $p1->getX();
        $zero = gmp_init(0, 10);
        if ($math->equals($r, $zero)) {
            throw new \RuntimeException("Error: random number R = 0");
        }

        $hash = $this->truncateHash($generator, $hash);
        $s = $modMath->div($modMath->add($hash, $math->mul($key->getSecret(), $r)), $k);
        if ($math->equals($s, $zero)) {
            throw new \RuntimeException("Error: random number S = 0");
        }

        return new Signature($r, $s);
    }

    /**
     * @param PublicKeyInterface $key
     * @param SignatureInterface $signature
     * @param \GMP $hash
     * @return bool
     */
    public function verify(PublicKeyInterface $key, SignatureInterface $signature, \GMP $hash)
    {

        $generator = $key->getGenerator();
        $n = $generator->getOrder();
        $r = $signature->getR();
        $s = $signature->getS();

        $math = $this->adapter;
        $one = gmp_init(1, 10);
        if ($math->cmp($r, $one) < 1 || $math->cmp($r, $math->sub($n, $one)) > 0) {
            return false;
        }

        if ($math->cmp($s, $one) < 1 || $math->cmp($s, $math->sub($n, $one)) > 0) {
            return false;
        }

        $modMath = $math->getModularArithmetic($n);
        $c = $math->inverseMod($s, $n);
        $u1 = $modMath->mul($hash, $c);
        $u2 = $modMath->mul($r, $c);
        $xy = $generator->mul($u1)->add($key->getPoint()->mul($u2));
        $v = $math->mod($xy->getX(), $n);

        return BinaryString::constantTimeCompare($math->toString($v), $math->toString($r));
    }
}
