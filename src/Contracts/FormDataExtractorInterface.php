<?php

namespace Craft\Contracts;

interface FormDataExtractorInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function extract(array $data): array;
}
