<?php

namespace Craft\Http\Validator\Rules;

class IntegerRule implements ValidationRuleInterface
{

    public function validate(string $attribute, mixed $value, array $params, Validator $validator): void
    {
        if (is_int($value) === false) {
            $validator->addError($attribute, 'integer');
        }
    }
}