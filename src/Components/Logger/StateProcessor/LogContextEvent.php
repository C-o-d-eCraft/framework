<?php

namespace Craft\Components\Logger\StateProcessor;

enum LogContextEvent: string
{
    case ATTACH_CONTEXT = self::class . '.ATTACH_CONTEXT';

    case DETACH_CONTEXT = self::class . '.DETACH_CONTEXT';

    case FLUSH_CONTEXT = self::class . '.FLUSH_CONTEXT';

    case ATTACH_EXTRAS = self::class . '.ATTACH_EXTRAS';

    case FLUSH_EXTRAS = self::class . '.FLUSH_EXTRAS';
}
