<?php

namespace Craft\Components\Logger\StateProcessor;

enum LogContextEvent: string
{
    case ATTACH_CONTEXT = 'ATTACH_CONTEXT';
    case DETACH_CONTEXT = 'DETACH_CONTEXT';
    case FLUSH_CONTEXT = 'FLUSH_CONTEXT';
    case ATTACH_EXTRAS = 'ATTACH_EXTRAS';
    case FLUSH_EXTRAS = 'FLUSH_EXTRAS';
}
