<?php

namespace Craft\Http\Validator\Rules;

use Craft\Contracts\ValidationRuleInterface;
use Craft\Http\Validator\Validator;

class EmailRule implements ValidationRuleInterface
{
    /**
     * @param string $attribute
     * @param mixed $value
     * @param array $params
     * @param Validator $validator
     * @return void
     */
    public function validate(string $attribute, mixed $value, array $params, Validator $validator): void
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $validator->addError($attribute, 'email');
        }
    }
}
