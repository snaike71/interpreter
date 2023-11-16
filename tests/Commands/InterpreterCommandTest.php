<?php

namespace Snaik\Interpreter\tests\Commands;

use Snaik\Interpreter\Commands\InterpreterCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class InterpreterCommandTest extends TestCase
{
    public function testItDoesNotCrash()
    {
        $command = new InterpreterCommand();

        $tester = new CommandTester($command);
        $tester->setInputs(["( + 5 5 )"]);
        $tester->execute([]);

        $tester->assertCommandIsSuccessful();

        $output = $tester->getDisplay();
        $tester->getOutput()->writeln("");
        $this->assertStringContainsString("🎉  Result : ",$output);
    }
}