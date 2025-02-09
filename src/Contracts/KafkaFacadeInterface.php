<?php

namespace Craft\Contracts;

interface KafkaFacadeInterface
{
    /**
     * @param string $topic
     * @param string $message
     * @return void
     */
    public function publish(string $topic, string $message): void;

    /**
     * @param string $topic
     * @param callable $callback
     * @return void
     */
    public function subscribe(string $topic, callable $callback): void;
}