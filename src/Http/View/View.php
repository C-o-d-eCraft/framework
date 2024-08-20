<?php

namespace Craft\Http\View;

use Craft\Contracts\ViewInterface;
use Craft\Http\Exceptions\NotFoundHttpException;
use Craft\Http\Exceptions\ViewNotFoundException;

class View implements ViewInterface
{
    public function __construct(private string $basePath = PROJECT_ROOT . 'src/view/')
    {
    }

    /**
     * @param string $basePath
     *
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
     *
     * @return false|string
     */
    public function render(string $view, array $params = []): false|string
    {
        $viewFilePath = $this->basePath . DIRECTORY_SEPARATOR . $view . '.php';

        if (file_exists($viewFilePath) === false) {
            throw new ViewNotFoundException("Представление файла '$view' не найдено.");
        }

        extract($params);

        ob_start();

        include $viewFilePath;

        return ob_get_clean();
    }
}
