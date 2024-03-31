<?php

namespace Craft\Contracts;

use Throwable;

interface ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     * @param string|null $statusCode
     * @param string|null $reasonPhrase
     * @return View
     */
    public function handle(Throwable $e, string $statusCode = null, string $reasonPhrase = null): string;
}