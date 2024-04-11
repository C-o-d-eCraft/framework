<?php

namespace Craft\Http\View;

use Craft\Contracts\ViewInterface;
use Craft\Http\Exceptions\HttpLogicException;
use InvalidArgumentException;

class View implements ViewInterface
{
    /**
     * @var string
     */
    private ?string $basePath = PROJECT_SOURCE_ROOT . 'view/';

    /**
     * @param string $basePath
     * @return void
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @param string $view
     * @param array $params
     * @return false|string
     */
    public function render(string $view, array $params = []): false|string
    {
        $viewFilePath = $this->basePath . DIRECTORY_SEPARATOR . $view . '.php';

        if (file_exists($viewFilePath) === false) {
            throw new HttpLogicException("Представление файла '$view' не найдено.");
        }

        extract($params);

        ob_start();

        include $viewFilePath;

        return ob_get_clean();
    }
}
