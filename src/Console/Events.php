<?php

namespace Craft\Console;

enum Events: string
{
    case BEFORE_EXECUTE = 'before_execute';
    case AFTER_EXECUTE = 'after_execute';
    case BEFORE_RUN = 'before_run';
    case OPTIONS_CONFIRM = 'options_confirm';
    case OPTION_CONFIRMED = 'option_confirmed';
    case FILE_SAVED = 'file_saved';
}
