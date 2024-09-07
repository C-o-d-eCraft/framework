<?php

namespace Craft\Http\Factory;

class FormNotFoundException extends \Exception
{
    public function __construct(string $formClassName)
    {
        parent::__construct("Форма с именем $formClassName не найдена.");
    }
}