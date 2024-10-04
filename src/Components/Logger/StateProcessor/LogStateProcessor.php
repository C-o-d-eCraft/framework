<?php

namespace Craft\Components\Logger\StateProcessor;

use Craft\Contracts\DebugTagStorageInterface;
use Craft\Contracts\LogStateProcessorInterface;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;

class LogStateProcessor implements LogStateProcessorInterface
{
    public function __construct(
        private string                   $indexName,
        private DebugTagStorageInterface $tagStorage,
        private LogStorageDTO            $storage
    ) {
        $this->storage->index = $this->indexName;
        $this->setUpDefaults();
    }

    /**
     * @return void
     */
    private function validateSetUp(): void
    {
        if (is_null($this->tagStorage->getTag()) === true) {
            throw new \InvalidArgumentException('Не определен X_DEBUG_TAG');
        }
    }

    /**
     * @return void
     */
    private function setUpDefaults(): void
    {
        $this->storage->actionType = empty($_SERVER['argv']) ? 'web' : 'cli';
    }

    /**
     * @param string $level
     * @param string $message
     * @param array|null $context
     *
     * @return object|LogStorageDTO
     * @throws \Exception
     */
    public function process(string $level, mixed $message): object
    {
        $this->validateSetUp();

        $this->storage->xDebugTag = $this->tagStorage->getTag();

        $this->storage->message = $message;

        if (
            $this->storage->message instanceof \Exception ||
            (class_exists(\Error::class) && $this->storage->message instanceof \Error)
        ) {
            $this->storage->exception = [
                'file' => $this->storage->message->getFile(),
                'line' => $this->storage->message->getLine(),
                'code' => $this->storage->message->getCode(),
                'trace' => explode(PHP_EOL, $this->storage->message->getTraceAsString()),
            ];
        }

        $this->storage->level = $level;

        $utcDate = new DateTime('now', new DateTimeZone('UTC'));

        $this->storage->datetime = $utcDate->format('Y-m-d\TH:i:s.uP');
        $this->storage->timestamp = (new DateTimeImmutable)->format('Y-m-d\TH:i:s.uP');

        return $this->storage;
    }
}
