<?php

namespace Craft\Components\Kafka;

use Craft\Contracts\KafkaFacadeInterface;
use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Enqueue\RdKafka\RdKafkaContext;
use Interop\Queue\Exception;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;

class KafkaFacade implements KafkaFacadeInterface
{
    private RdKafkaContext $context;

    public function __construct(array $config)
    {
        $factory = new RdKafkaConnectionFactory($config);

        $this->context = $factory->createContext();
    }

    /**
     * @param string $topic
     * @param string $message
     * @return void
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function publish(string $topic, string $message): void
    {
        $destination = $this->context->createTopic($topic);
        $msg = $this->context->createMessage($message);

        $producer = $this->context->createProducer();
        $producer->send($destination, $msg);
    }

    /**
     * @param string $topic
     * @param callable $callback
     * @return void
     */
    public function subscribe(string $topic, callable $callback): void
    {
        $destination = $this->context->createTopic($topic);
        $consumer = $this->context->createConsumer($destination);

        while (true) {
            $message = $consumer->receive();

            $continue = $callback($message->getBody());
            if ($continue === false) {
                break;
            }

            $consumer->acknowledge($message);
        }
    }
}