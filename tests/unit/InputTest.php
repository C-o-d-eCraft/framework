<?php

namespace Tests\Unit;

use Craft\Console\Input;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    public function testArgumentsIsSetCorrectly(): void
    {
        $input = new Input(['script.php', 'namespace', 'arg1', 'arg2', '--option1', '--option2=value', 'arg3']);

        $this->assertEquals(['arg1', 'arg2', 'arg3'], $input->getArguments());
    }

    public function testOptionsIsSetCorrectly(): void
    {
        $input = new Input(['script.php', 'namespace', 'arg1', 'arg2', '--option1', '--option2=value', 'arg3']);

        $this->assertEquals(['--option1', '--option2=value'], $input->getOptions());
    }

    public function testCommandNameSpaceIsSetCorrectly(): void
    {
        $input = new Input(['script.php', 'namespace', 'arg1', 'arg2', '--option1', '--option2=value', 'arg3']);

        $this->assertEquals('namespace', $input->getCommandNameSpace());
    }
}
