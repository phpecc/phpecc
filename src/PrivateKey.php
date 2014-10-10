<?php
namespace Mdanter\Ecc;

/**
 * *********************************************************************
 * Copyright (C) 2012 Matyas Danter
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * ***********************************************************************
 */

/**
 * This class serves as public - private key exchange for signature verification.
 */
class PrivateKey implements PrivateKeyInterface
{

    private $publicKey;

    private $secretMultiplier;

    private $adapter;

    public function __construct(PublicKeyInterface $publicKey, $secretMultiplier, MathAdapter $adapter)
    {
        $this->publicKey = $publicKey;
        $this->secretMultiplier = $secretMultiplier;
        $this->adapter = $adapter;
    }

    /**
     *
     * @return PublicKeyInterface
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * (non-PHPdoc)
     * @see \Mdanter\Ecc\PrivateKeyInterface::sign()
     */
    public function sign($hash, $random_k)
    {
        $math = $this->adapter;

        $G = $this->publicKey->getGenerator();
        $n = $G->getOrder();
        $k = $math->mod($random_k, $n);
        $p1 = $G->mul($k);
        $r = $p1->getX();

        if ($math->cmp($r, 0) == 0) {
            throw new \RuntimeException("error: random number R = 0 <br />");
        }

        $s = $math->mod($math->mul($math->inverseMod($k, $n), $math->mod($math->add($hash, $math->mul($this->secretMultiplier, $r)), $n)), $n);

        if ($math->cmp($s, 0) == 0) {
            throw new \RuntimeException("error: random number S = 0<br />");
        }

        return new Signature($r, $s);
    }
}
