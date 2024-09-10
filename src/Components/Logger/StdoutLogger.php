<?php

namespace Craft\Components\Logger;

use Craft\Contracts\LogStateProcessorInterface;

class StdoutLogger extends AbstractLogger
{
    public function __construct(private LogStateProcessorInterface $logStateProcessor)
    {
        parent::__construct($this->logStateProcessor);
    }

    protected function writeLog(mixed $logMessage): void
    {
        $fileHandle = fopen('php://stdout', 'w');

        if ($fileHandle === false) {
            error_log("Не удалось открыть php://stdout для записи");

            return;
        }

        fwrite($fileHandle, $logMessage);
        fclose($fileHandle);
    }
}
