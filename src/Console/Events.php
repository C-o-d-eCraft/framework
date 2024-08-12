<?php

namespace Craft\Console;

class Events
{
    /**
     * @param string $message
     */
    const BEFORE_EXECUTE = 'before_execute';

    /**
     * @param string $message
     */
    const AFTER_EXECUTE = 'after_execute';

    /**
     * @param string $message
     */
    const BEFORE_RUN = 'before_run';

    /**
     * @param string $message
     */
    const OPTIONS_CONFIRM = 'options_confirm';

    /**
     * @param string $message
     */
    const OPTION_CONFIRMED = 'option_confirmed';
    
    const FILE_SAVED = 'file_saved';
}
