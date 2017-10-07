## Pure PHP Elliptic Curve DSA and DH

[![Build Status](https://travis-ci.org/phpecc/phpecc.svg?branch=master)](https://travis-ci.org/phpecc/phpecc)
[![HHVM Status](http://hhvm.h4cc.de/badge/mdanter/ecc.svg)](http://hhvm.h4cc.de/package/mdanter/ecc)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpecc/phpecc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpecc/phpecc?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/phpecc/phpecc/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phpecc/phpecc/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/mdanter/ecc/v/stable.png)](https://packagist.org/packages/mdanter/ecc)
[![Total Downloads](https://poser.pugx.org/mdanter/ecc/downloads.png)](https://packagist.org/packages/mdanter/ecc)
[![Latest Unstable Version](https://poser.pugx.org/mdanter/ecc/v/unstable.png)](https://packagist.org/packages/mdanter/ecc)
[![License](https://poser.pugx.org/mdanter/ecc/license.png)](https://packagist.org/packages/mdanter/ecc)

### Information

This library is a rewrite/update of Matyas Danter's ECC library. All credit goes to him.

For more information on Elliptic Curve Cryptography please read [this fine article](http://www.matyasdanter.com/2010/12/elliptic-curve-php-oop-dsa-and-diffie-hellman/).

The library supports the following curves:

 - secp112r1
 - secp256k1
 - nistp192
 - nistp224
 - nistp256 / secg256r1
 - nistp384 / secg384r1
 - nistp521

The library exposes a class for random byte generation, for PHP7+ users is provided by `random_bytes`. 
PHP5.6 users will use the paragonie/random_bytes polyfill. 

During ECDSA, a random value `k` is required. It is acceptable to use a true RNG to generate this value, but 
should the same `k` value ever be repeatedly used for a key, an attacker can recover that signing key. 
The HMAC random generator can derive a deterministic k value from the message hash and private key, voiding
this concern.

The library uses a non-branching Montgomery ladder for scalar multiplication, as it's constant time and avoids secret 
dependant branches. 

### Compatibility notice:

A dependency used by this library in v0.4.x interacts with a newly reserved keyword in PHP 7.2. Therefore the library
cannot be used on 7.2. For a PHP7.0+ branch, you should upgrade to [v0.5.x](https://github.com/phpecc/phpecc/releases/tag/v0.5.0)

 
### License

This package is released under the MIT license.

### Requirements

* PHP 5.6+
* composer
* ext-gmp

### Installation

You can install this library via Composer :

`composer require mdanter/ecc`

### Contribute

When sending in pull requests, please make sure to run the `make` command.

The default target runs all PHPUnit and PHPCS tests. All tests
must validate for your contribution to be accepted.

It's also always a good idea to check the results of the [Scrutinizer analysis](https://scrutinizer-ci.com/g/phpecc/phpecc/) for your pull requests.

### Usage

Examples:
 * [Key generation](./examples/key_generation.php)
 * [ECDH exchange](./examples/ecdh_exchange.php)
 * [Signature creation](./examples/creating_signature.php)
 * [Signature verification](./examples/verify_signature.php)
