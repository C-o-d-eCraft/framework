<?php

namespace Craft\Http\Validator;

class AbstractFormRequest
{
    /**
     * Массив с правилами валидации для формы,
     * проверят объект Request
     *
     * @return array
     */


public function rules(): array
{
    return [];
}
}