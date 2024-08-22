<?php

namespace Craft\Contracts;

interface UnixProcessServiceInterface
{
    public function fork(): int;

    public function getPid(): int;

    public function setSid(): int;
}
