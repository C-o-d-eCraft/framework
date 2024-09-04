<?php

namespace Craft\Contracts;

interface FormDataExtractorInterface
{
    public function extract(array $data, array $requiredKeys = []): array;
}
