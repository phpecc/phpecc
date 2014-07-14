## Pure PHP Elliptic Curve DSA and DH

[![Build Status](https://travis-ci.org/mdanter/phpecc.svg?branch=master)](https://travis-ci.org/mdanter/phpecc)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mdanter/phpecc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mdanter/phpecc?branch=master)
### Foreword

This library is a rewrite/update of Matys Danter's ECC library. All credit goes to him.

### Information

For more information on Elliptic Curve Cryptography please read http://matejdanter.com/?cat=14

### License

This package is released under the MIT license.

### Requirements

* PHP 5.3+
* composer
* ext-bcmath or ext-gmp. GMP math is highly recommended due to performance considerations (10x faster than BCMath)

### Installation

You can install this library via Composer :

`composer require mdanter/ecc:0.1`


