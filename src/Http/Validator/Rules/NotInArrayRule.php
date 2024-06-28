<?php

namespace Craft\Http\Validator\Rules;


use Craft\Http\Validator\Validator;


class NotInArrayRule implements ValidationRuleInterface
{

    public function validate(string $attribute, mixed $value, array $params, Validator $validator): void
    {
        if (in_array($value, $params, true) === true) {
            $validator->addError($attribute, 'notInArray', ['values' => implode(', ', $params)]);
        }
    }
}