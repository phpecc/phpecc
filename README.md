## Pure PHP Elliptic Curve DSA and DH

[![Latest Stable Version](https://poser.pugx.org/mdanter/ecc/v/stable.png)](https://packagist.org/packages/mdanter/ecc)
[![Total Downloads](https://poser.pugx.org/mdanter/ecc/downloads.png)](https://packagist.org/packages/mdanter/ecc)
[![Latest Unstable Version](https://poser.pugx.org/mdanter/ecc/v/unstable.png)](https://packagist.org/packages/mdanter/ecc)
[![License](https://poser.pugx.org/mdanter/ecc/license.png)](https://packagist.org/packages/mdanter/ecc)

### Information

This library is an actively maintained fork of Thomas Kerin's [`phpecc`](https://github.com/phpecc/phpecc) library, which is a rewrite/update of Matyas Danter's ECC library. All credit goes to them.

For more information on Elliptic Curve Cryptography please read [this fine article](http://www.matyasdanter.com/2010/12/elliptic-curve-php-oop-dsa-and-diffie-hellman/).

The library supports the following curves:

 - secp112r1
 - secp256k1
 - nistp192
 - nistp224
 - nistp256 / secp256r1
 - nistp384 / secp384r1
 - nistp521

During ECDSA, a random value `k` is required. It is acceptable to use a true RNG to generate this value, but 
should the same `k` value ever be repeatedly used for a key, an attacker can recover that signing key. 
The HMAC random generator can derive a deterministic k value from the message hash and private key, voiding
this concern.

The library uses a non-branching Montgomery ladder for scalar multiplication, as it's constant time and avoids secret 
dependant branches. 
 
### License

This package is released under the MIT license.

### Requirements

* PHP 7.0+ or PHP 8.0+
* composer
* ext-gmp

Support for older PHP versions:
 * v0.4.x: php ^5.6|<7.2
 * v0.5.x: php ^7.0
 * v1.0.x: php ^7.0|^8.0

### Installation

You can install this library via Composer :

`composer require shanecurran/phpecc`

### Contribute

When sending in pull requests, please make sure to run the `make` command.

### Usage

Examples:
 * [Key generation](./examples/key_generation.php)
 * [ECDH exchange](./examples/ecdh_exchange.php)
 * [Signature creation](./examples/creating_signature.php)
 * [Signature verification](./examples/verify_signature.php)
