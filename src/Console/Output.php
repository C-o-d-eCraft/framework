<?php

namespace Craft\Console;

use Craft\Contracts\OutputInterface;

class Output implements OutputInterface
{
    /**
     * @var string
     */
    public string $message = '';

    /**
     * @var int
     */
    public int $statusCode = 0;

    /**
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return void
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param string $result
     * @return void
     */
    public function stdout(string $result): void
    {
        fwrite(STDOUT, $result);
    }

    /**
     * @param string $result
     * @return void
     */
    public function info(string $result): void
    {
        $this->stdout("\033[34m" . $result . "\033[0m");
    }

    /**
     * @param string $result
     * @return void
     */
    public function warning(string $result): void
    {
        $this->stdout("\033[38;5;214m" . $result . "\033[0m");
    }

    /**
     * @param string $result
     * @return void
     */
    public function success(string $result): void
    {
        $this->stdout("\033[32m" . $result . "\033[0m");
    }

    /**
     * @param string $result
     * @return void
     */
    public function primary(string $result): void
    {
        $this->stdout("\033[34m" . $result . "\033[0m");
    }
}
