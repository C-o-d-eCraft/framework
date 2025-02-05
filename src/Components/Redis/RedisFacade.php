<?php

namespace Craft\Components\Redis;

use Craft\Contracts\RedisFacadeInterface;
use Psr\Cache\InvalidArgumentException;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisFacade implements RedisFacadeInterface
{
    private Redis $redis;
    private RedisAdapter $cacheAdapter;

    public function __construct(array $config)
    {
        $this->redis = new Redis();
        $this->redis->connect($config['host'], $config['port']);

        $this->cacheAdapter = new RedisAdapter(
            $this->redis,
            $config['namespace'],
            $config['default_ttl']
        );
    }

    /**
     * @param string $key
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getItem(string $key): mixed
    {
        $item = $this->cacheAdapter->getItem($key);

        if ($item->isHit() === false) {
            return null;
        }

        return $item->get();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     * @return void
     * @throws InvalidArgumentException
     */
    public function setItem(string $key, mixed $value, ?int $ttl = null): void
    {
        $item = $this->cacheAdapter->getItem($key);
        $item->set($value);

        if ($ttl !== null) {
            $item->expiresAfter($ttl);
        }

        $this->cacheAdapter->save($item);
    }

    /**
     * @param string $channel
     * @param array $data
     * @return void
     */
    public function publish(string $channel, array $data): void
    {
        $this->redis->publish($channel, json_encode($data));
    }

    /**
     * @param array $channels
     * @param callable $callback
     * @return void
     */
    public function subscribe(array $channels, callable $callback): void
    {
        $this->redis->subscribe($channels, $callback);
    }
}