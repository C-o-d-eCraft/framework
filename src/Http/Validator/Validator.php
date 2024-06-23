<?php

namespace Craft\Http\Validator;


use Exception;

class Validator
{
    /**
     * Правила валидации.
     *
     * @var array
     */
    protected array $rules;

    /**
     * Ошибки валидации.
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * @param array $rules Правила валидации.
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Применяет правило к данным.
     *
     * @param array $rule Правило валидации.
     * @param array $data Данные для валидации.
     * @throws Exception
     */
    protected function applyRule(array $rule, array $data): void
    {
       [$attributes, $ruleName, $params] = array_pad($rule, 3, null);

        foreach ((array) $attributes as $attribute) {
            $method = 'validate' . ucfirst($ruleName);
            if (method_exists($this, $method) === false) {
                throw new Exception("Правило валидации {$ruleName} не существует.");
            }

            $this->$method($attribute, $data[$attribute] ?? null, $params);
        }
    }

    /**
     * @param array $data Данные для валидации.
     * @throws Exception
     * @return bool
     */
    public function validate(array $data): bool
    {
        $result = true;

        foreach ($this->rules as $rule) {
            $this->applyRule($rule, $data);
        }
        if (empty($this->errors) === false) {
            $result = false;
        }

        return $result;
    }

    /**
     * Возвращает ошибки валидации.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     *
     * @param string $attribute Имя атрибута.
     * @param string $message Сообщение об ошибке.
     * @param array $params Дополнительные параметры для сообщения.
     */
    protected function addError(string $attribute, string $message, array $params = []): void
    {
        $message = str_replace(':attribute', $attribute, $message);
        foreach ($params as $key => $value) {
            $message = str_replace(":{$key}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    /**
     *
     * @param string $attribute Имя атрибута.
     * @param mixed $value Значение атрибута.
     */
    protected function validateRequired(string $attribute, mixed $value): void
    {
        if (empty($value) === true) {
            $this->addError($attribute, $this->getMessage('required'));
        }
    }

    /**
     *
     * @param string $attribute Имя атрибута.
     * @param mixed $value Значение атрибута.
     */
    protected function validateString(string $attribute, mixed $value): void
    {
        if (is_string($value) === false) {
            $this->addError($attribute, $this->getMessage('string'));
        }
    }

    /**
     *
     * @param string $attribute Имя атрибута.
     * @param mixed $value Значение атрибута.
     */
    protected function validateNumeric(string $attribute, mixed $value): void
    {
        if (is_numeric($value) === false) {
            $this->addError($attribute, $this->getMessage('numeric'));
        }
    }

    /**
     *
     * @param string $attribute Имя атрибута.
     * @param mixed $value Значение атрибута.
     */
    protected function validateInteger(string $attribute, mixed $value): void
    {
        if (is_int($value) === false) {
            $this->addError($attribute, $this->getMessage('integer'));
        }
    }

    /**
     *
     * @param string $attribute Имя атрибута.
     * @param mixed $value Значение атрибута.
     * @param array $params Массив допустимых значений.
     */
    protected function validateInArray(string $attribute, mixed $value, array $params): void
    {
        if (in_array($value, $params, true) === false) {
            $this->addError($attribute, $this->getMessage('inArray'), ['values' => implode(', ', $params)]);
        }
    }

    /**
     *
     * @param string $attribute Имя атрибута.
     * @param mixed $value Значение атрибута.
     * @param array $params Массив недопустимых значений.
     */
    protected function validateNotInArray(string $attribute, mixed $value, array $params): void
    {
        if (in_array($value, $params, true) === true) {
            $this->addError($attribute, $this->getMessage('notInArray'), ['values' => implode(', ', $params)]);
        }
    }

    /**
     *
     * @param string $attribute Имя атрибута.
     * @param mixed $value Значение атрибута.
     */
    protected function validateEmail(string $attribute, mixed $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($attribute, $this->getMessage('email'));
        }
    }

    /**
     *
     * @param string $attribute Имя атрибута.
     * @param mixed $value Значение атрибута.
     */
    protected function validateBoolean(string $attribute, mixed $value): void
    {
        if (is_bool($value) === false) {
            $this->addError($attribute, $this->getMessage('boolean'));
        }
    }

    /**
     *
     * @param string $attribute Имя атрибута.
     * @param mixed $value Значение атрибута.
     */
    protected function validateDate(string $attribute, mixed $value): void
    {
        if (strtotime($value) === false) {
            $this->addError($attribute, $this->getMessage('date'));
        }
    }

    /**
     * Возвращает сообщение об ошибке для правила.
     *
     * @param string $rule Имя правила валидации.
     * @return string
     */
    protected function getMessage(string $rule): string
    {
        $messages = ValidationMessages::getErrorsMessages();
        return $messages[$rule] ?? 'Ошибка валидации';
    }
}
