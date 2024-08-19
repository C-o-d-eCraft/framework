<?php

namespace Craft\Http\Validator\Rules;

use Craft\Http\Validator\Validator;
use Craft\Contracts\ValidationRuleInterface;

class RequiredRule implements ValidationRuleInterface
{
    /**
     * @param string|array $attribute
     * @param mixed $value
     * @param array $params
     * @param Validator $validator
     * @return void
     */
    public function validate(string|array $attributes, mixed $value, array $params, Validator $validator): void
    {
        if (is_array($attributes) === false) {
            $attributes = [$attributes];
        }

        foreach ($attributes as $attr) {
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
        if (empty($value) === true) {
            $validator->addError($attribute, 'required');
        }
    }
}
