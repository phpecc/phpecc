<?php
/***********************************************************************
Copyright (C) 2012 Matyas Danter

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
*************************************************************************/
include dirname(__DIR__) . '/vendor/autoload.php';

$seconds = 7200;
set_time_limit($seconds);

//define('USE_EXT', 'BCMATH');

if (extension_loaded('gmp') && ! defined('USE_EXT')) {
    define('USE_EXT', 'GMP');
}
elseif (extension_loaded('bcmath') && ! defined('USE_EXT')) {
    define('USE_EXT', 'BCMATH');
}

// verbosity for test methods
$verbose = false;

$t = new \Mdanter\Ecc\Tests\TestSuite($verbose);

?>
