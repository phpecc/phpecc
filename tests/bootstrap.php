<?php

function buildPath() {
    return implode(DIRECTORY_SEPARATOR, func_get_args());
}

define('TEST_DATA_DIR', buildPath(__DIR__, 'data'));

include buildPath(__DIR__, '..', 'vendor', 'autoload.php');

