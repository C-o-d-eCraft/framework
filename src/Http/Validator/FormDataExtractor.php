<?php

namespace Craft\Http\Validator;

use Craft\Contracts\FormDataExtractorInterface;

class FormDataExtractor implements FormDataExtractorInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function extract(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value) === false) {
                return (array)$value;
            }
        }

        return [];
    }
}
