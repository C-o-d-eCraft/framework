<?php

namespace Craft\Http\Validator\Rules;


use Craft\Http\Validator\Validator;

class BooleanRule implements ValidationRuleInterface
{

    public function validate(string $attribute, mixed $value, array $params, Validator $validator): void
    {
        if (is_bool($value) === false) {
            $validator->addError($attribute, 'boolean');
        }
    }
}