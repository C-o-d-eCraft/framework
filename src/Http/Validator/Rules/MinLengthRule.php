<?php

namespace Craft\Http\Validator\Rules;

use Craft\Contracts\ValidationRuleInterface;
use Craft\Http\Validator\Validator;

class MinLengthRule implements ValidationRuleInterface
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
        $minLength = (int)$params[0];

        if (strlen($value) < $minLength) {
            $validator->addError($attribute, 'minLength', ['min' => $minLength]);
        }
    }
}