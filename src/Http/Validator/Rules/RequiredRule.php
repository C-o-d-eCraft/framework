<?php

namespace Craft\Http\Validator\Rules;

use Craft\Http\Validator\Validator;

class RequiredRule implements ValidationRuleInterface
{
    public function validate(string|array $attribute, mixed $value, array $params, Validator $validator): void
    {
        if (is_array($attribute)) {
            foreach ($attribute as $attr) {
                if (empty($value[$attr])) {
                    $validator->addError($attr, 'required');
                }
            }
        } else {
            if (empty($value)) {
                $validator->addError($attribute, 'required');
            }
        }
    }
}
