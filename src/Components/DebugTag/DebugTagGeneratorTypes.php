<?php

namespace Craft\Components\DebugTag;

enum DebugTagGeneratorTypes: string
{
    case MODE_TYPE_CLI = 'cli';
    case MODE_TYPE_WEB = 'web';
}
