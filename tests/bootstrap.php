<?php

function buildPath()
{
    return implode(DIRECTORY_SEPARATOR, func_get_args());
}

define('TEST_DATA_DIR', buildPath(__DIR__, 'data'));

if (getenv('MATH_LIB') === false) {
    echo 'MATH_LIB env var is not defined, defaulting to GMP'.PHP_EOL;
    define('MATH_LIB', 'gmp');
} else {
    define('MATH_LIB', getenv('MATH_LIB'));
}

include buildPath(__DIR__, '..', 'vendor', 'autoload.php');
