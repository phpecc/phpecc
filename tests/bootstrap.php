<?php

echo PHP_EOL . 'Running legacy test suite...' . PHP_EOL;

function buildPath() {
    return implode(DIRECTORY_SEPARATOR, func_get_args());
}

define('TEST_DATA_DIR', buildPath(__DIR__, 'data'));

include buildPath(__DIR__, '..', 'vendor', 'autoload.php');
$oldTestSuite = include buildPath(__DIR__, 'legacy-runner.php');

if ($oldTestSuite !== 0) {
    echo PHP_EOL . 'Initial test suite FAILURE, stopping...' . PHP_EOL . PHP_EOL;
    exit(1);
}
else {
    echo 'Initial test suite successful, continuing...' . PHP_EOL . PHP_EOL;
}