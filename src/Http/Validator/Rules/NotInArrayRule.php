<?php

namespace Craft\Http\Validator\Rules;

use Craft\Contracts\ValidationRuleInterface;
use Craft\Http\Validator\Validator;

class NotInArrayRule implements ValidationRuleInterface
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
        if (in_array($value, $params, true) === true) {
            $validator->addError($attribute, 'notInArray', ['values' => implode(', ', $params)]);
        }
    }
}
