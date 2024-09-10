<?php

namespace Craft\Components\Logger;

use Craft\Contracts\LogStateProcessorInterface;

class FileLogger extends AbstractLogger
{
    private string $logFilePath = '';
    
    public function __construct(
        private LogStateProcessorInterface $logStateProcessor,
        private string $logDir = PROJECT_ROOT . 'runtime/app-logs',
        private string $logFileName = 'app-web.log',
        private string $loggingMode = 'daily'
    ) {
        parent::__construct($this->logStateProcessor);

        if (is_dir($logDir) === false) {
            mkdir($logDir, 644, true);
        }

        $this->logFilePath = $this->determineLogFilePath();
    }

    protected function writeLog(mixed $logMessage): void
    {
        file_put_contents($this->logFilePath, $logMessage . PHP_EOL, FILE_APPEND);
    }

    private function determineLogFilePath(): string
    {
        if ($this->loggingMode === 'daily') {
            return $this->logDir . '/' . $this->generateDailyLogFileName();
        }

        return $this->logDir . '/' . $this->logFileName;
    }

    private function generateDailyLogFileName(): string
    {
        $date = date('Y-m-d');
        $fileInfo = pathinfo($this->logFileName);

        return $fileInfo['filename'] . '-' . $date . '.' . $fileInfo['extension'];
    }
}
