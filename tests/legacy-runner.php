<?php
use Mdanter\Ecc\Theory\Bc;
use Mdanter\Ecc\NumberTheory;
use Mdanter\Ecc\Tests\TestSuite;
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

function runSuite($t, $gmp) {
    $module = $gmp ? 'GMP' : 'BCMATH';
    echo '-- using ' . $module . ' math module.' . PHP_EOL;
    ob_start();

    $errors = $t->run($gmp);

    if ($errors !== 0) {
        ob_end_flush();
    } else {
        ob_end_clean();
    }

    return $errors;
}

$seconds = 7200;
set_time_limit($seconds);

// verbosity for test methods
$verbose = true;
$errors = 0;
$t = new TestSuite($verbose);

\Mdanter\Ecc\ModuleConfig::useGmp();
if (\Mdanter\Ecc\ModuleConfig::hasGmp()) {
    $errors += runSuite($t, true);
}

\Mdanter\Ecc\ModuleConfig::useBcMath();
NumberTheory::setTheoryAdapter(new Bc(NumberTheory::$smallprimes));
if (\Mdanter\Ecc\ModuleConfig::hasBcMath()) {
    $errors += runSuite($t, false);
}

return $errors;