<?php

namespace Tests\Unit;

use Craft\Console\Input;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    public function testOnlyArguments(): void
    {
        $argv = ['script.php', 'namespace', 'arg1', 'arg2', 'arg3'];
        $input = new Input($argv);

        $this->assertEquals(['arg1', 'arg2', 'arg3'], $input->getArguments());
    }

    public function testOnlyOptions(): void
    {
        $argv = ['script.php', 'namespace', '--option1', '--option2=value', '--option3'];
        $input = new Input($argv);

        $this->assertEquals(['--option1', '--option2=value', '--option3'], $input->getOptions());
    }

    public function testArgumentsAndOptions(): void
    {
        $argv = ['script.php', 'namespace', 'arg1', 'arg2', '--option1', '--option2=value', 'arg3'];
        $input = new Input($argv);

        $this->assertEquals(['arg1', 'arg2', 'arg3'], $input->getArguments());
        $this->assertEquals(['--option1', '--option2=value'], $input->getOptions());
    }

    public function testCommandNameSpace(): void
    {
        $argv = ['script.php', 'namespace', 'arg1', 'arg2', '--option1', '--option2=value', 'arg3'];
        $input = new Input($argv);

        $this->assertEquals('namespace', $input->getCommandNameSpace());
    }
}
