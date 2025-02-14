<?php

namespace Craft\Components\DebugTag;

use Craft\Contracts\DebugTagGeneratorInterface;
use Craft\Contracts\DebugTagStorageInterface;

class DebugTagGenerator implements DebugTagGeneratorInterface
{
    public function __construct(
        private DebugTagStorageInterface $tagStorage,
        private string $indexName,
        private string $mode = DebugTagGeneratorTypes::MODE_TYPE_WEB->value
    ) {
    }

    /**
     * @param Application $app
     * @return void
     */
    public function init(): void
    {
        if ($this->mode === DebugTagGeneratorTypes::MODE_TYPE_CLI->value) {
            $this->refreshTag();
        }

        $this->createFromHeaders();

        if ($this->tagStorage->getTag() !== null) {
            return;
        }

        if (empty($this->indexName)) {
            throw new \InvalidArgumentException('Не задано имя приложения. Внесите доработку в конфигурацию приложения');
        }

        $key = 'x-debug-tag-' . $this->indexName . '-';
        $key .= uniqid();
        $key .= '-' . gethostname() . '-' . time();

        $this->tagStorage->setTag(md5($key));
    }

    /**
     * @return array
     */
    private function getHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * @return void
     */
    private function createFromHeaders(): void
    {
        $headers = $this->getHeaders();

        if (isset($headers['X-Debug-Tag']) === true) {
            $this->tagStorage->setTag($headers['X-Debug-Tag']);
        }
    }

    /**
     * @return void
     */
    public function refreshTag(): void
    {
        if ($this->mode === DebugTagGeneratorTypes::MODE_TYPE_WEB->value) {
            throw new \RuntimeException('Невозможно обновить X_DEBUG_TAG в режиме web');
        }

        $key = 'x-debug-tag-' . $this->indexName . '-';
        $key .= uniqid();
        $key .= '-' . gethostname() . '-' . time();

        $this->tagStorage->setTag(md5($key));
    }
}
