<?php

namespace Craft\Components\Logger\StateProcessor;

use Craft\Contracts\DTOInterface;

class LogStorageDTO
{
    public ?string $index = null;
    public ?string $x_debug_tag = null;
    public ?string $action_type = null;
    public ?string $context = null;
    public ?string $level = null;
    public mixed $message = null;
    public ?string $datetime = null;
    public ?string $timestamp = null;
    public mixed $exception = null;
    public ?array $extras = null;
}
