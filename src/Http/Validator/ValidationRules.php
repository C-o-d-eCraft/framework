<?php

namespace Craft\Http\Validator;

class ValidationRules
{
    public static function rulesApiAddMonth(array $monthsRepository): array
    {
        return [
            'unique' => [
                ['month', $monthsRepository],
            ],
            'required' => [
                ['month'],
            ],
        ];
    }

    public static function rulesApiDeleteMonth(array $monthsRepository): array
    {
        return [
            'exists' => [
                ['id', $monthsRepository],
            ],
            'required' => [
                ['id'],
            ],
        ];
    }
    public static function rulesCalculatorCalculate(): array
    {
        return [
            'required' => [
                ['month'],
                ['tonnage'],
                ['raw_type'],
            ],
            'integer' => [
                ['tonnage'],
            ],
        ];
    }
}