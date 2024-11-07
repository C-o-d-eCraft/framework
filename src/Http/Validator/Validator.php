<?php

namespace Craft\Http\Validator;

use Craft\Contracts\ValidationRuleInterface;
use InvalidArgumentException;

class Validator
{
    public array $errors = [];
    public array $data;

    protected array $messages = [
        'required' => 'Поле :attribute обязательно для заполнения.',
        'string' => 'Поле :attribute должно быть строкой.',
        'integer' => 'Поле :attribute должно быть целым числом.',
        'numeric' => 'Поле :attribute должно быть числом.',
        'inArray' => 'Поле :attribute не существует.',
        'notInArray' => 'Поле :attribute уже существует.',
        'email' => 'Поле :attribute должно быть адресом электронной почты.',
        'boolean' => 'Поле :attribute должно быть истинным или ложным.',
        'date' => 'Поле :attribute должно быть действительной датой.',
        'minLength' => 'Поле :attribute должно быть не менее :min символов.',
        'maxLength' => 'Поле :attribute должно быть не более :max символов.',
    ];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param array $rule
     * @return void
     */
    public function apply(array $rule): void
    {
        [$attributes, $ruleName, $params] = array_pad($rule, 3, []);

        if (is_callable($ruleName)) {
            if (is_array($attributes) === false) {
                $attributes = [$attributes];
            }

            foreach ($attributes as $attr) {
                $ruleName($this->data[$attr] ?? null, $attr);
            }

            return;
        }

        if (strpos($ruleName, '=') !== false) {
            [$ruleName, $parameter] = explode('=', $ruleName, 2);
            $params[] = $parameter;
        }

        $ruleClass = $this->getRuleClass($ruleName);

        $this->validateAttributes($attributes, $ruleClass, $params);
    }

    /**
     * @param string $ruleName
     * @return ValidationRuleInterface
     */
    protected function getRuleClass(string $ruleName): ValidationRuleInterface
    {
        $rulesNamespace = 'Craft\\Http\\Validator\\Rules\\';
        $className = $rulesNamespace . ucfirst($ruleName) . 'Rule';

        if (class_exists($className) === true) {
            return new $className();
        }

        throw new InvalidArgumentException("Правило валидации {$ruleName} не существует.");
    }

    /**
     * @param array|string $attributes
     * @param ValidationRuleInterface $ruleClass
     * @param array $params
     * @return void
     */
    protected function validateAttributes(array|string $attributes, ValidationRuleInterface $ruleClass, array $params): void
    {
        if (is_array($attributes) === false) {
            $attributes = [$attributes];
        }

        foreach ($attributes as $attr) {
            $ruleClass->validate($attr, $this->data[$attr] ?? null, $params, $this);
        }
    }

    /**
     * @param string $attribute
     * @param string $rule
     * @param array $params
     * @return void
     */
    public function addError(string $attribute, string $rule, array $params = []): void
    {
        $message = $this->messages[$rule] ?? 'Ошибка валидации';
        $message = str_replace(':attribute', $attribute, $message);

        foreach ($params as $key => $value) {
            $message = str_replace(":{$key}", $value, $message);
        }

        $this->errors[$attribute][] = $message;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
