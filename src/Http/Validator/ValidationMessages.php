<?php
namespace Craft\Http\Validator;

class ValidationMessages
{
    public static function getErrorsMessages(): array
    {
        return [
            'required' => 'Поле :attribute обязательно для заполнения.',
            'string' => 'Поле :attribute должно быть строкой.',
            'integer' => 'Поле :attribute должно быть целым числом.',
            'numeric' => 'Поле :attribute должно быть числом.',
            'inArray' => 'Поле :attribute не существует.',
            'notInArray' => 'Поле :attribute уже существует',
            'email' => 'Поле :attribute должно быть адресом электронной почты.',
            'boolean' => 'Поле :attribute должно быть истинным или ложным.',
            'date' => 'Поле :attribute должно быть действительной датой.',
        ];
    }
}
