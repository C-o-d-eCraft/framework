<?php

namespace Craft\Http\Validator\Rules;


use Craft\Http\Validator\Validator;

class StringRule implements ValidationRuleInterface
{

    public function validate(string $attribute, mixed $value, array $params, Validator $validator): void
    {
        if (is_string($value) === false) {
            $validator->addError($attribute, 'string');
        }
    }
}