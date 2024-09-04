<?php

namespace Craft\Http\Validator;

use Craft\Contracts\FormDataExtractorInterface;

class FormDataExtractor implements FormDataExtractorInterface
{
    /**
     * @param array $data
     * @param array $requiredKeys
     * @return array
     */
    public function extract(array $data, array $requiredKeys = []): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value) === false) {
                $value = (array)$value;
            }

            if ($this->hasRequiredKeys($value, $requiredKeys)) {
                return $value;
            }
        }

        return [];
    }

    /**
     * @param array $data
     * @param array $requiredKeys
     * @return bool
     */
    private function hasRequiredKeys(array $data, array $requiredKeys): bool
    {
        foreach ($requiredKeys as $key) {
            if (array_key_exists($key, $data) === false) {
                return false;
            }
        }

        return true;
    }
}
