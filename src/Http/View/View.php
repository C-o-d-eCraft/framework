<?php

namespace Craft\Http\View;

use InvalidArgumentException;

readonly class View
{
    public function __construct(private string $basePath) { }

    /**
     * @param string $view
     * @param array $params
     * @return false|string
     */
    public function render(string $view, array $params = []): false|string
    {
        $viewFilePath = $this->basePath . DIRECTORY_SEPARATOR . $view;

        if (file_exists($viewFilePath) === false) {
            throw new InvalidArgumentException("Представление файла '$view' не найдено.");
        }

        extract($params);

        ob_start();

        include $viewFilePath;

        return ob_get_clean();
    }
}
