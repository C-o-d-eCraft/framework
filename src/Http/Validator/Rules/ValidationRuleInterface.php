<?php

namespace Craft\Http\Validator\Rules;

use Craft\Http\Validator\Validator;

interface ValidationRuleInterface
{
    /**
     * @param string $attribute
     * @param mixed $value
     * @param array $params
     * @param Validator $validator
     * @return void
     */
    public function validate(string $attribute, mixed $value, array $params, Validator $validator): void;
}
