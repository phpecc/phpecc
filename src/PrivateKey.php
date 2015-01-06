<?php

namespace Mdanter\Ecc;

use Mdanter\Ecc\EcMath;
use Mdanter\Ecc\Signature\Signer;

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
    private $generator;

    private $secretMultiplier;

    private $adapter;

    public function __construct(MathAdapterInterface $adapter, GeneratorPoint $generator, $secretMultiplier)
    {
        $this->adapter = $adapter;
        $this->generator = $generator;
        $this->secretMultiplier = $secretMultiplier;
    }

    public function getPublicKey()
    {
        return new PublicKey($this->generator, $this->generator->mul($this->secretMultiplier), $this->adapter);
    }

    public function getPoint()
    {
        return $this->generator;
    }

    public function getSecret()
    {
        return $this->secretMultiplier;
    }

    public function establishSharedKey(PublicKeyInterface $key)
    {
        return $key->getPoint()->mul($this->secretMultiplier)->getX();
    }

}
