<?php

namespace Craft\Http\Validator\Rules;

use Craft\Http\Validator\Validator;

class RequiredRule implements ValidationRuleInterface
{
    public function validate(string|array $attribute, mixed $value, array $params, Validator $validator): void
    {
        foreach ((array)$attribute as $attr) {
            $this->validateAttribute($attr, $value, $validator);
        }
    }

    private function validateAttribute(string $attribute, mixed $value, Validator $validator): void
    {
        if (empty($value)) {
            $validator->addError($attribute, 'required');
        }
    }
}
