<?php

namespace Craft\Http\Validator\Rules;

use Craft\Http\Validator\Validator;

class NumericRule implements ValidationRuleInterface
{
    public function validate(string $attribute, mixed $value, array $params, Validator $validator): void
    {
        if (!is_numeric($value)) {
            $validator->addError($attribute, 'numeric');
        }
    }
}
