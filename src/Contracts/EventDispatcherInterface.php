<?php

namespace Framework\Contracts;

use app\DTO\Message;
use app\Observers\ObserverInterface;

interface EventDispatcherInterface
{
    /**
     * Подписывает наблюдателя к определенному событию
     *
     * @param string $event
     * @param ObserverInterface $observer Наблюдатель, который будет присоединен
     * @return void
     */
    public function attach(string $event, ObserverInterface $observer): void;

    /**
     * Отписывает наблюдателя от определенного события
     *
     * @param string $event
     * @return void
     */
    public function detach(string $event): void;

    /**
     * Запускает событие и уведомляет соответствующего наблюдателя с переданным сообщением
     *
     * @param string $event
     * @param Message|null $message Сообщение, передаваемое наблюдателю
     * @return void
     */
    public function trigger(string $event, Message|null $message): void;
}
