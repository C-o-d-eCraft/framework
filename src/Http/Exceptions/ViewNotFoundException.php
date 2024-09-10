<?php

namespace Craft\Http\Exceptions;

class ViewNotFoundException extends \Exception
{
    public function __construct(string $filePath)
    {
        parent::__construct("Представление файла '$filePath' не найдено.");
    }
}
