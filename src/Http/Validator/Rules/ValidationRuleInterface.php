<?php

namespace Craft\Http\Validator\Rules;

use Craft\Http\Validator\Validator;

interface ValidationRuleInterface
{
    public function validate(string $attribute, mixed $value, array $params, Validator $validator): void;
}