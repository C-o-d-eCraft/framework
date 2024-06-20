<?php

namespace Craft\Components\Logger\StateProcessor;

use Craft\Contracts\DTOInterface;

class LogStorageDTO
{
    /**
     * @var string
     */
    public $index;

    /**
     * @var array
     */
    public $context = [];

    /**
     * @var string
     */
    public $level;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $action_type;

    /**
     * @var string
     */
    public $datetime;

    /**
     * @var string
     */
    public $timestamp;

    /**
     * @var int|null
     */
    public $userId = null;

    /**
     * @var string|null
     */
    public $ip = null;

    /**
     * @var string|null
     */
    public $real_ip = null;

    /**
     * @var string
     */
    public $x_debug_tag;

    /**
     * @var string
     */
    public $message;

    /**
     * @var mixed|null
     */
    public $exception = null;

    /**
     * @var string|null
     */
    public $extras = null;

}