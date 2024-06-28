<?php

namespace Craft\Http\Validator\Rules;


use Craft\Http\Validator\Validator;

class DateRule implements ValidationRuleInterface
{

    public function validate(string $attribute, mixed $value, array $params, Validator $validator): void
    {
        if (strtotime($value) === false) {
            $validator->addError($attribute, 'date');
        }
    }
}