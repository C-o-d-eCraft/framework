<?php

namespace Tests\Unit;

use Craft\Console\InputArguments;
use PHPUnit\Framework\TestCase;

class InputArgumentsTest extends TestCase
{
    public function testRequiredArgumentParseAndSetCorrectly(): void
    {
        $inputArgument = new InputArguments('testRequiredArg');

        $this->assertTrue($inputArgument->required);
    }

    public function testRequiredArgumentIsDefaultNull(): void
    {
        $inputArgument = new InputArguments('testRequiredArg');

        $this->assertNull($inputArgument->defaultValue);
    }

    public function testOptionalArgumentParseAndSetCorrectly(): void
    {
        $inputArgument = new InputArguments('?testOptionalArg=42');

        $this->assertFalse($inputArgument->required);
    }

    public function testArgumentWithDefaultValueParseAndSetCorrectly(): void
    {
        $inputArgument = new InputArguments('?testOptionalArg=42');

        $this->assertEquals(42, $inputArgument->defaultValue);
    }

    public function testOptionalArgumentIsDefaultNotNull(): void
    {
        $inputArgument = new InputArguments('?testOptionalArg=42');

        $this->assertNotNull($inputArgument->defaultValue);
    }

    public function testOptionalArgumentWithoutDefaultValueThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Необязательный аргумент должен иметь значение по умолчанию!');

        new InputArguments('?testOptionalArg');
    }

    public function testArgumentNameParseAndSetCorrectly(): void
    {
        $inputArgument = new InputArguments('testArg');

        $this->assertEquals('testArg', $inputArgument->name);
    }
}
