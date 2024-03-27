<?php

namespace Craft\Components\Logger;

use Craft\Contracts\LoggerInterface;
use DateTimeImmutable;

class Logger implements LoggerInterface
{
    /**
     * @var string
     */
    protected string $indexName = 'REFLEG';

    /**
     * @var string
     */
    public string $handleContext = 'Расчет стоимости';

    /**
     * @return string
     */
    public function getXDebugTag(): string
    {
        return md5('x-debug-tag-' . $this->indexName . '-' . rand(1e9, 9e9) . '-' . gethostname() . time());
    }

    /**
     * @param string|\Throwable $message
     * @param string $category
     * @param string $context
     * @param mixed|null $extras
     * @param string $xDebugTag
     * @return void
     * @throws \Exception
     */
    public function writeLog(
        string|\Throwable $message,
        string $category,
        string $context,
        mixed $extras = null,
        string $xDebugTag = '',
    ): void
    {
        $file = PROJECT_ROOT . '/runtime/app-log-' . date('Y-m-d') . '.log';

        if ($xDebugTag === '') {
            $xDebugTag = $this->getXDebugTag();
        }
        
        if (file_exists($file) === false) {
            touch($file);
        }

        $utcDate = new \DateTime('now', new \DateTimeZone('UTC'));

        $exception = [];

        if (
            $message instanceof \Throwable
        ) {
            $exception = [
                'file' => $message->getFile(),
                'line' => $message->getLine(),
                'code' => $message->getCode(),
                'trace' => explode(PHP_EOL, $message->getTraceAsString()),
            ];

            $message = $message->getMessage();
        }
        $state = [
            'index' => $this->indexName,
            'category' => $category,
            'context' => $context,
            'level' => empty($exception) === false ? 1 : 2,
            'level_name' => empty($exception) === false ? 'error' : 'info',
            'action' => '/',
            'action_type' => 'web',
            'datetime' => $utcDate->format('Y-m-d\TH:i:s.uP'),
            'timestamp' => (new DateTimeImmutable)->format('Y-m-d\TH:i:s.uP'),
            'userId' => null,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'real_ip' => $_SERVER['REMOTE_ADDR'],
            'x_debug_tag' => $xDebugTag,
            'message' => $message instanceof \Throwable ? $message->getMessage() : $message,
            'exception' => $exception,
            'extras' => empty($extras) === false ? $extras : null,
        ];

        $line = json_encode((array)$state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        file_put_contents($file, $line . PHP_EOL, FILE_APPEND);
    }
}
