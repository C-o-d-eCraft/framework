<?php

namespace Craft\Components\Logger;

class DebugTagGenerator
{
    /**
     * @param  Application $app
     * @return void
     */
    public function init(): void
    {
        $this->createFromHeaders();

        if (defined('X_DEBUG_TAG') === true) {
            return;
        }

        if (empty(getenv('INDEX_NAME'))) {
            throw new \InvalidArgumentException('Не задано имя приложения. Внесите доработку в конфигурацию приложения');
        }

        $key = 'x-debug-tag-' . getenv('INDEX_NAME') . '-';
        $key .= uniqid();
        $key .= '-' . gethostname() . '-' . time();

        define('X_DEBUG_TAG', md5($key));
    }

    /**
     * @return void
     */
    private function getHeaders(): void
    {
        if (function_exists('getallheaders') === false) {
            function getallheaders() {
                $headers = [];
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }

                return $headers;
            }
        }
    }

    /**
     * @return void
     */
    private function createFromHeaders(): void
    {
        $headers = $this->getHeaders();

        if (isset($headers['X-Debug-Tag']) === false) {
            return;
        }

        define('X_DEBUG_TAG', $headers['X-Debug-Tag']);
    }
}
