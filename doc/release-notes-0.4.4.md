# v0.4.4 release notes

v0.4.4 is a minor release for the library.

It includes a security patch for EcDH -- the received public point 
is checked to exist on the private key's curve before doing ECDH.
If you are using ECDH, it is highly recommended that you upgrade. 

It also includes a performance enhancement that reduces the time
for scalar multiplication by approximately 14%.

   - [(#213)](https://github.com/phpecc/phpecc/pull/213)  provide function for serializing bigints with padding
   - [(#214)](https://github.com/phpecc/phpecc/pull/214)  Constant size for field element conditional swap
   - [(#215)](https://github.com/phpecc/phpecc/pull/215)  EcDH: check public point is valid on our curve
   - [(#217)](https://github.com/phpecc/phpecc/pull/217)  GeneratorPoint::getPublicKeyFrom - remove unused param

## Credits
Thanks to everyone who directly contributed to this release:

 - Thomas Kerin (@afk11)
