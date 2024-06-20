<?php

namespace Craft\Components\Logger\StateProcessor;

use Craft\Components\Logger\Observers\ObserverAttachContext;
use Craft\Components\Logger\Observers\ObserverAttachExtras;
use Craft\Components\Logger\Observers\ObserverDetachContext;
use Craft\Components\Logger\Observers\ObserverFlushExtras;
use Craft\Components\Logger\Observers\ObserverFlushContext;
use Craft\Contracts\LogStateProcessorInterface;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\ObserverInterface;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;


class LogStateProcessor implements LogStateProcessorInterface
{
    private LogStorageDTO $storage;

    /**
     * @param string $index
     */
    public function __construct(string $index)
    {
        $this->storage = new LogStorageDTO();
        $this->storage->index = $index;

        $this->setUpDefaults();
    }

    private function validateSetUp()
    {
        if (defined('X_DEBUG_TAG') === false) {
            throw new \InvalidArgumentException('Не определена константа логирования инцидентов X_DEBUG_TAG');
        }
    }

    /**
     * @return void
     */
    private function setUpDefaults(): void
    {
        $this->storage->action_type = empty($_SERVER['argv']) ? 'web' : 'cli';
    }

    /**
     * @return string
     */
    private function defineAction(): string
    {
        if (empty($_SERVER['argv'])) {
            return $_SERVER['REQUEST_URI'];
        }

        if (empty($_SERVER['SCRIPT_NAME']) === false) {
            return $_SERVER['SCRIPT_NAME'];
        }

        return 'Обработчик не опеределен';
    }

    /**
     * @param string $level
     * @param string $message
     * @param array|null $context
     * @return object|LogStorageDTO
     * @throws \Exception
     */
    public function process(string $level, string $message, array $extras = []): object
    {
        $this->validateSetUp();

        $storage = clone $this->storage;

        $storage->context = $storage->context !== null ? implode(':', $storage->context) : $storage->context = 'No context';

        $storage->message = $message;

        $storage->level = $level;

        if (
            $storage->message instanceof Exception
            ||
            (class_exists(\Error::class) && $storage->message instanceof \Error)
        ) {
            $storage->exception = [
                'file' => $storage->message->getFile(),
                'line' => $storage->message->getLine(),
                'code' => $storage->message->getCode(),
                'trace' => explode(PHP_EOL, $storage->message->getTraceAsString()),
            ];

            $storage->message = $storage->message->getMessage();
        }

        $utcDate = new DateTime('now', new DateTimeZone('UTC'));

        $storage->datetime = $utcDate->format('Y-m-d\TH:i:s.uP');
        $storage->timestamp = (new DateTimeImmutable)->format('Y-m-d\TH:i:s.uP');

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) === true) {
            $realIpList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $storage->real_ip = array_shift($realIpList);
        }

        $storage->level = $level;

        $storage->action = $this->defineAction();

        $storage->userId = null;

        $storage->ip = isset($_SERVER['HTTP_X_REAL_IP']) === true ? $_SERVER['HTTP_X_REAL_IP'] : null;

        $storage->x_debug_tag = X_DEBUG_TAG;

        $storage->extras = $extras ?? null;

        return $storage;
    }
}