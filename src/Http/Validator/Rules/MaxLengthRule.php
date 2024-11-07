<?php

namespace Craft\Http\Validator\Rules;

use Craft\Contracts\ValidationRuleInterface;
use Craft\Http\Validator\Validator;

class MaxLengthRule implements ValidationRuleInterface
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
        $maxLength = (int)$params[0];

        if (strlen($value) > $maxLength) {
            $validator->addError($attribute, 'maxLength', ['max' => $maxLength]);
        }
    }
}