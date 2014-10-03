<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Infinity;

class InfinityTest extends \PHPUnit_Framework_TestCase
{

    public function testInfinityEqualsItself()
    {
        $infinity = Infinity::getInstance();

        $this->assertTrue($infinity->equals($infinity));
    }
}
