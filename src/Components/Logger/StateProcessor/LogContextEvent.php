<?php

namespace Craft\Components\Logger\StateProcessor;

class LogContextEvent
{
    const ATTACH_CONTEXT = 'ATTACH_CONTEXT';

    const DETACH_CONTEXT = 'DETACH_CONTEXT';

    const FLUSH_CONTEXT = 'FLUSH_CONTEXT';

    const ATTACH_EXTRAS = 'ATTACH_EXTRAS';

    const FLUSH_EXTRAS = 'FLUSH_EXTRAS';
}
