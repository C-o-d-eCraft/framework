<?php

namespace Craft\Http\Validator\Rules;

use Craft\Http\Validator\Validator;

class RequiredRule implements ValidationRuleInterface
{
    /**
     * @param string|array $attribute
     * @param mixed $value
     * @param array $params
     * @param Validator $validator
     * @return void
     */
    public function validate(string|array $attribute, mixed $value, array $params, Validator $validator): void
    {
        foreach ((array)$attribute as $attr) {
            $this->validateAttribute($attr, $value, $validator);
        }
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param Validator $validator
     * @return void
     */
    private function validateAttribute(string $attribute, mixed $value, Validator $validator): void
    {
        if (empty($value)) {
            $validator->addError($attribute, 'required');
        }
    }
}
