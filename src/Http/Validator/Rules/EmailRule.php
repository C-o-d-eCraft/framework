<?php

namespace Craft\Http\Validator\Rules;


use Craft\Http\Validator\Validator;

class EmailRule implements ValidationRuleInterface
{

    public function validate(string $attribute, mixed $value, array $params, Validator $validator): void
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $validator->addError($attribute, 'email');
        }
    }
}