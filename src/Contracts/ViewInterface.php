<?php

namespace Craft\Contracts;

interface ViewInterface
{

    /**
     * @param string $view
     * @param array $params
     * @return false|string
     */
    public function render(string $view, array $params = []): false|string;
}