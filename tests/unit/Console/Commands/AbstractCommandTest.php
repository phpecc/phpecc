<?php

namespace Mdanter\Ecc\Tests\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use Mdanter\Ecc\Tests\AbstractTestCase;

abstract class AbstractCommandTest extends AbstractTestCase
{

    /**
     *
     * @param  Command                                         $command
     * @param  unknown                                         $name
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    protected function getCommandTester(Command $command, $name)
    {
        $application = new Application();
        $application->add($command);

        $command = $application->find($name);

        return new CommandTester($command);
    }

    /**
     * Strip all spaces, line returns, and tabs
     * @param  string $string
     * @return string
     */
    protected function normalize($string)
    {
        $string = str_replace(' ', '', $string);
        $string = str_replace(PHP_EOL, '', $string);
        $string = str_replace("\n", '', $string);
        $string = str_replace("\r", '', $string);
        $string = str_replace("\t", '', $string);

        return $string;
    }
}
