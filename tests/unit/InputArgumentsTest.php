<?php

namespace Tests\Unit;

use Craft\Console\InputArguments;
use PHPUnit\Framework\TestCase;

class InputArgumentsTest extends TestCase
{
    public function testRequiredArgument(): void
    {
        $argument = 'requiredArg';
        $inputArgument = new InputArguments($argument);

        $this->assertEquals('requiredArg', $inputArgument->name);
        $this->assertTrue($inputArgument->required);
        $this->assertNull($inputArgument->defaultValue);
    }

    public function testOptionalArgument(): void
    {
        $argument = '?optionalArg';
        $inputArgument = new InputArguments($argument);

        $this->assertEquals('optionalArg', $inputArgument->name);
        $this->assertFalse($inputArgument->required);
        $this->assertNull($inputArgument->defaultValue);
    }

    public function testArgumentWithDefaultValue(): void
    {
        $argument = 'argWithDefault=42';
        $inputArgument = new InputArguments($argument);

        $this->assertEquals('argWithDefault', $inputArgument->name);
        $this->assertTrue($inputArgument->required);
        $this->assertEquals(42, $inputArgument->defaultValue);
    }

    public function testOptionalArgumentWithDefaultValue(): void
    {
        $argument = '?optionalArgWithDefault=99';
        $inputArgument = new InputArguments($argument);

        $this->assertEquals('optionalArgWithDefault', $inputArgument->name);
        $this->assertFalse($inputArgument->required);
        $this->assertEquals(99, $inputArgument->defaultValue);
    }
}
