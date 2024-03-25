<?php

namespace Craft\Http\View;

use InvalidArgumentException;

readonly class View
{
    /**
     * @var string 
     */
    private string $pageNotFound;
    
    public function __construct(private string $basePath) 
    { 
        $this->pageNotFound = $this->basePath . DIRECTORY_SEPARATOR . 'NotFound.php';
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        $viewFilePath = $this->basePath . DIRECTORY_SEPARATOR . $view;

        if (file_exists($viewFilePath) === false) {
            throw new InvalidArgumentException("Представление файла '$view' не найдено.");
        }

        extract($params);

        ob_start();

        include $viewFilePath;

        return ob_get_clean() ?? $this->pageNotFound;
    }
}
