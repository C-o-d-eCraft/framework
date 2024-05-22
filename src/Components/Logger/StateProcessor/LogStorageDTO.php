<?php

namespace Craft\Components\Logger\StateProcessor;

class LogStorageDTO
{
    /**
     * @var string
     */
    public $index;

    /**
     * @var string
     */
    public $category;

    /**
     * @var string|null
     */
    public $context = null;

    /**
     * @var int
     */
    public $level;

    /**
     * @var string
     */
    public $level_name;

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